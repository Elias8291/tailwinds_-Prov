<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DiasInhabiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DetalleTramiteController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::with('user');

        // Aplicar filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('motivo', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->get('fecha_hasta'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'fecha_hora');
        $sortDirection = $request->get('direction', 'desc');
        
        $validSorts = ['fecha_hora', 'estado', 'created_at'];
        if (in_array($sortBy, $validSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Paginación
        $perPage = $request->get('perPage', 10);
        $citas = $query->paginate($perPage)->appends($request->query());
        
        $diasInhabiles = DiasInhabiles::orderBy('fecha_inicio', 'desc')->get();

        return view('citas.index', compact('citas', 'diasInhabiles'));
    }

    public function create()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        // Obtener datos de domicilio si hay un trámite en progreso
        $datosDomicilio = [];
        $datosApoderado = [];
        if ($solicitante) {
            $tramiteEnProgreso = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();
                
            if ($tramiteEnProgreso) {
                try {
                    $detalleTramiteController = new DetalleTramiteController();
                    $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramiteEnProgreso->id);
                    
                    if ($datosDomicilio) {
                        Log::info('📍 CITAS CREATE: Datos de domicilio obtenidos', [
                            'tramite_id' => $tramiteEnProgreso->id,
                            'codigo_postal' => $datosDomicilio['codigo_postal'],
                            'estado' => $datosDomicilio['estado'],
                            'municipio' => $datosDomicilio['municipio']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('📍 CITAS CREATE: Error al obtener datos de domicilio', [
                        'tramite_id' => $tramiteEnProgreso->id,
                        'error' => $e->getMessage()
                    ]);
                    $datosDomicilio = [];
                }
                
                // Obtener datos del apoderado legal si es persona moral
                if ($tramiteEnProgreso && $tramiteEnProgreso->solicitante->tipo_persona === 'Moral') {
                    $apoderadoController = new \App\Http\Controllers\Formularios\ApoderadoLegalController();
                    $datosApoderado = $apoderadoController->getDatosApoderadoLegal($tramiteEnProgreso);
                    $datosApoderado['tramite_id'] = $tramiteEnProgreso->id;
                }
            }
        }
        
        return view('citas.create', compact('solicitante', 'datosDomicilio', 'datosApoderado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'notas' => 'nullable|string',
            'tramite_id' => 'nullable|exists:tramite,id',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante. Por favor, complete su registro primero.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'No se encontró información del solicitante. Por favor, complete su registro primero.');
        }

        // Si viene tramite_id, verificar que el usuario sea el propietario
        if ($request->tramite_id) {
            $tramite = Tramite::find($request->tramite_id);
            if (!$tramite || $tramite->solicitante_id !== $solicitante->id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tiene permisos para agendar cita para este trámite.'
                    ], 403);
                }
                return redirect()->back()
                    ->with('error', 'No tiene permisos para agendar cita para este trámite.');
            }
        }

        // Check if date is not a weekend (optional business rule)
        $fechaCita = \Carbon\Carbon::parse($request->fecha_hora);
        if ($fechaCita->isWeekend()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden agendar citas en fines de semana.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'No se pueden agendar citas en fines de semana.');
        }

        // Check for existing appointments at the same time
        $citaExistente = Cita::where('fecha_hora', $request->fecha_hora)
                            ->where('estado', '!=', 'cancelada')
                            ->first();
        
        if ($citaExistente) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una cita agendada para esa fecha y hora. Por favor, seleccione otro horario.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Ya existe una cita agendada para esa fecha y hora. Por favor, seleccione otro horario.');
        }

        try {
            // Determinar el user_id correcto
            $userId = $user->id; // Por defecto, el usuario actual
            
            // Si hay un tramite_id, asignar la cita al solicitante del trámite
            if ($request->tramite_id) {
                $tramite = Tramite::with('solicitante')->find($request->tramite_id);
                if ($tramite && $tramite->solicitante && $tramite->solicitante->usuario_id) {
                    $userId = $tramite->solicitante->usuario_id;
                }
            }
            
            $cita = Cita::create([
                'user_id' => $userId,
                'fecha_hora' => $request->fecha_hora,
                'motivo' => $request->motivo,
                'notas' => $request->notas,
                'estado' => 'pendiente',
                'tramite_id' => $request->tramite_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cita agendada correctamente.',
                    'cita' => $cita
                ]);
            }

            // Si viene de un trámite específico, redirigir al estado del trámite
            if ($request->tramite_id) {
                return redirect()->route('tramites.solicitante.estado', $request->tramite_id)
                    ->with('success', 'Cita agendada correctamente.');
            }

            return redirect()->route('citas.index')
                ->with('success', 'Cita agendada correctamente.');
                
        } catch (\Exception $e) {
            Log::error('Error al crear cita: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al agendar la cita. Por favor, intente nuevamente.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al agendar la cita. Por favor, intente nuevamente.');
        }
    }

    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
            'notas' => 'nullable|string',
        ]);

        $cita->update($request->all());

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $cita->update(['estado' => $request->estado]);

        return redirect()->back()
            ->with('success', 'Estado de la cita actualizado correctamente.');
    }

    public function mostrarFormularioAgendar($tramiteId)
    {
        $tramite = \App\Models\Tramite::findOrFail($tramiteId);
        
        // Verificar que el trámite esté aprobado
        if ($tramite->estado !== 'Aprobado') {
            return redirect()->route('tramites.solicitante.estado', $tramite->id)
                ->with('error', 'El trámite debe estar aprobado para agendar una cita.');
        }
        
        // Verificar que el usuario es el propietario del trámite
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
            abort(403, 'No tiene permisos para acceder a este trámite.');
        }
        
        return view('citas.agendar', compact('tramite'));
    }

    /**
     * Agendar cita específica para un trámite
     */
    public function agendar(Request $request)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'notas' => 'nullable|string',
            'tramite_id' => 'required|exists:tramite,id',
        ]);

        try {
            // Obtener el trámite con la relación del solicitante
            $tramite = Tramite::with('solicitante')->findOrFail($request->tramite_id);
            
            // Verificar que no exista ya una cita para este trámite
            $citaExistente = Cita::where('tramite_id', $tramite->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->first();

            if ($citaExistente) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'Ya existe una cita programada para este trámite. Fecha: ' . 
                               $citaExistente->fecha_hora->format('d/m/Y H:i')
                ], 200);
            }

            // Verificar que la fecha no sea fin de semana
            $fechaCita = \Carbon\Carbon::parse($request->fecha_hora);
            if ($fechaCita->isWeekend()) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No se pueden agendar citas en fines de semana'
                ], 200);
            }

            // Verificar que no haya conflicto de horario (máximo 4 citas por hora)
            $citasEnHorario = Cita::where('fecha_hora', $request->fecha_hora)
                ->where('estado', '!=', 'cancelada')
                ->count();

            if ($citasEnHorario >= 4) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No hay disponibilidad en ese horario. Máximo 4 citas por hora. Por favor, seleccione otro horario.'
                ], 200);
            }

            // Verificar que el trámite tenga un solicitante válido
            if (!$tramite->solicitante || !$tramite->solicitante->usuario_id) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No se pudo encontrar información del solicitante para este trámite'
                ], 200);
            }

            // Crear la cita asignada al solicitante del trámite
            $cita = Cita::create([
                'user_id' => $tramite->solicitante->usuario_id,
                'tramite_id' => $tramite->id,
                'fecha_hora' => $request->fecha_hora,
                'motivo' => $request->motivo,
                'notas' => $request->notas,
                'estado' => 'pendiente',
            ]);

            Log::info('🗓️ CITA AGENDADA para trámite', [
                'tramite_id' => $tramite->id,
                'cita_id' => $cita->id,
                'fecha_hora' => $request->fecha_hora,
                'motivo' => $request->motivo,
                'solicitante_id' => $tramite->solicitante->usuario_id,
                'usuario_que_agenda' => Auth::id(),
                'solicitante_existe' => $tramite->solicitante ? 'si' : 'no',
                'usuario_id_existe' => $tramite->solicitante && $tramite->solicitante->usuario_id ? 'si' : 'no'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cita agendada correctamente para el solicitante del trámite',
                'cita' => [
                    'id' => $cita->id,
                    'fecha_hora' => $fechaCita->format('d/m/Y H:i'),
                    'motivo' => $cita->motivo,
                    'estado' => $cita->estado
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR al agendar cita para trámite', [
                'tramite_id' => $request->tramite_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al agendar la cita'
            ], 500);
        }
    }

    /**
     * Completar cita de cotejo físico y crear proveedor
     */
    public function completarCotejo(Request $request, Cita $cita)
    {
        $request->validate([
            'resultado' => 'required|in:exitoso,fallido',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que es una cita de cotejo
            if (!str_contains($cita->motivo, 'Cotejo físico')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta cita no es de cotejo físico'
                ], 400);
            }

            $exitoso = $request->resultado === 'exitoso';
            
            // Completar la cita
            $tramite = $cita->completarCotejo($exitoso, $request->observaciones);
            
            if ($exitoso && $tramite) {
                // Crear el proveedor después del cotejo exitoso
                $proveedor = \App\Models\Proveedor::crearDesdeTramiite($tramite);
                
                // Cambiar estado del trámite a "Aprobado"
                $tramite->update([
                    'estado' => 'Aprobado',
                    'observaciones' => ($tramite->observaciones ?? '') . "\nCotejo físico completado exitosamente."
                ]);
                
                // Cambiar rol de usuario
                if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                    $usuario = \App\Models\User::find($tramite->solicitante->usuario_id);
                    if ($usuario) {
                        $usuario->removeRole('Solicitante');
                        $usuario->assignRole('Proveedor');
                    }
                }
                
                // Enviar notificación de éxito
                if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                    \App\Models\Notificacion::crearParaUsuario(
                        '🎉 ¡Ya eres Proveedor Oficial!',
                        "¡Felicitaciones! El cotejo físico de documentos fue exitoso. " .
                        "Su código de proveedor oficial es: " . $proveedor->pv . ". " .
                        "Ya puede participar en licitaciones del Gobierno del Estado de Oaxaca.",
                        'Informativo',
                        $tramite->solicitante->usuario_id
                    );
                }
                
                Log::info('Cotejo completado exitosamente y proveedor creado:', [
                    'cita_id' => $cita->id,
                    'tramite_id' => $tramite->id,
                    'proveedor_pv' => $proveedor->pv
                ]);
                
                $mensaje = 'Cotejo completado exitosamente. Proveedor creado con código: ' . $proveedor->pv;
                
            } else {
                // Cotejo fallido
                if ($tramite) {
                    $tramite->update([
                        'estado' => 'Rechazado',
                        'observaciones' => ($tramite->observaciones ?? '') . "\nCotejo físico fallido: " . ($request->observaciones ?? 'Sin observaciones')
                    ]);
                    
                    // Enviar notificación de fallo
                    if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                        \App\Models\Notificacion::crearParaUsuario(
                            '❌ Cotejo Físico No Exitoso',
                            "El cotejo físico de documentos no fue exitoso. " .
                            "Observaciones: " . ($request->observaciones ?? 'Sin observaciones específicas') . ". " .
                            "Por favor, corrija los puntos observados y solicite una nueva cita.",
                            'Advertencia',
                            $tramite->solicitante->usuario_id
                        );
                    }
                }
                
                $mensaje = 'Cotejo marcado como fallido. Se ha notificado al solicitante.';
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al completar cotejo:', [
                'cita_id' => $cita->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al completar el cotejo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar citas de cotejo pendientes
     */
    public function citasCotejo(Request $request)
    {
        $query = Cita::with(['user', 'tramite.solicitante'])
                    ->where('motivo', 'like', '%Cotejo físico%')
                    ->whereIn('estado', ['pendiente', 'confirmada']);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora', $request->fecha);
        }

        $citas = $query->orderBy('fecha_hora', 'asc')->get();

        return view('citas.cotejo', compact('citas'));
    }

    /**
     * Mostrar dashboard de citas de hoy
     */
    public function dashboardCotejo()
    {
        $citasHoy = Cita::with(['user', 'tramite.solicitante'])
                        ->where('motivo', 'like', '%Cotejo físico%')
                        ->whereDate('fecha_hora', today())
                        ->whereIn('estado', ['pendiente', 'confirmada'])
                        ->orderBy('fecha_hora', 'asc')
                        ->get();

        $citasProximas = Cita::with(['user', 'tramite.solicitante'])
                            ->where('motivo', 'like', '%Cotejo físico%')
                            ->where('fecha_hora', '>', now())
                            ->whereIn('estado', ['pendiente', 'confirmada'])
                            ->orderBy('fecha_hora', 'asc')
                            ->limit(5)
                            ->get();

        return view('citas.dashboard-cotejo', compact('citasHoy', 'citasProximas'));
    }

    public function validar(Cita $cita)
    {
        return view('citas.validar', [
            'cita' => $cita->load(['tramite.solicitante'])
        ]);
    }

    /**
     * Obtiene el siguiente día hábil disponible para citas
     */
    public function siguienteDiaDisponible(Request $request, $tramiteId)
    {
        try {
            // Obtener el trámite
            $tramite = Tramite::findOrFail($tramiteId);
            
            // Obtener la fecha actual
            $fecha = now()->addDay();
            
            // Buscar el siguiente día hábil disponible
            while (true) {
                // Verificar si es fin de semana
                if ($fecha->isWeekend()) {
                    $fecha->addDay();
                    continue;
                }
                
                // Verificar si es día inhábil
                $diaInhabil = DiasInhabiles::where(function($query) use ($fecha) {
                    $query->where('fecha_inicio', '<=', $fecha)
                          ->where(function($q) use ($fecha) {
                              $q->where('fecha_fin', '>=', $fecha)
                                ->orWhereNull('fecha_fin');
                          });
                })->exists();
                
                if ($diaInhabil) {
                    $fecha->addDay();
                    continue;
                }
                
                // Verificar disponibilidad de horarios
                $citasExistentes = Cita::whereDate('fecha_hora', $fecha->format('Y-m-d'))->count();
                if ($citasExistentes < 10) { // Máximo 10 citas por día
                    // Encontramos un día disponible
                    break;
                }
                
                $fecha->addDay();
            }
            
            // Asignar hora (9:00 AM)
            $fechaHora = $fecha->setHour(9)->setMinute(0)->setSecond(0);
            
            return response()->json([
                'success' => true,
                'fecha_disponible' => $fechaHora->format('Y-m-d H:i:s'),
                'message' => 'Fecha disponible encontrada'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar fecha disponible: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reagendar una cita existente
     */
    public function reagendar(Request $request, Tramite $tramite)
    {
        // Verificar si el trámite ya tuvo una reagendación previa
        $citasAnteriores = $tramite->citas()
            ->where('motivo_reagendacion', 'identificacion_no_coincide')
            ->count();

        if ($citasAnteriores > 0) {
            // Ya tuvo una reagendación previa, cancelar el trámite
            return redirect()->route('tramites.cancelar', [
                'tramite' => $tramite->id,
                'motivo' => 'excede_intentos_identificacion'
            ]);
        }

        // Obtener la cita actual
        $citaActual = $tramite->citas()->where('estado', 'pendiente')->first();
        
        if ($citaActual) {
            // Marcar la cita actual como reagendada
            $citaActual->update([
                'estado' => 'reagendada',
                'motivo_reagendacion' => 'identificacion_no_coincide'
            ]);
        }

        // Redirigir a la vista de reagendación
        return redirect()->route('citas.reagendada', [
            'tramite' => $tramite->id,
            'motivo' => 'La identificación presentada no coincide con el documento digital'
        ])->with('warning', 'Por favor, seleccione una nueva fecha para su cita. Recuerde traer una identificación oficial válida que coincida con la registrada en el sistema.');
    }

    /**
     * Muestra la vista de reagendación de cita
     */
    public function mostrarReagendacion(Request $request, Tramite $tramite)
    {
        return view('citas.reagendada', [
            'tramite' => $tramite,
            'motivo' => $request->get('motivo')
        ]);
    }
} 