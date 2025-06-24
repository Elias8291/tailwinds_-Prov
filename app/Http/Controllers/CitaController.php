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
            $cita = Cita::create([
                'user_id' => $user->id,
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
} 