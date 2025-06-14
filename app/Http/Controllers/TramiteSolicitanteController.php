<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\Documento;
use Carbon\Carbon;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\DetalleTramiteController;

class TramiteSolicitanteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tipoTramite = $this->determinarTipoTramite($user);
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        // Obtener datos de domicilio si hay un trámite en progreso
        $datosDomicilio = [];
        if ($tramiteEnProgreso) {
            Log::info('Trámite en progreso encontrado:', [
                'tramite_id' => $tramiteEnProgreso->id,
                'tipo_tramite' => $tramiteEnProgreso->tipo_tramite
            ]);
            
            $datosDomicilio = $this->obtenerDatosDomicilio($tramiteEnProgreso);
        }
        
        return view('tramites.solicitante.index', compact('tipoTramite', 'user', 'tramiteEnProgreso', 'datosDomicilio'));
    }

    private function determinarTipoTramite($user)
    {
        // Simulando lógica de negocio - ajustar según tus modelos reales
        
        // Si no tiene proveedor o su PV ya venció: INSCRIPCIÓN
        if (!$user->proveedor || $this->proveedorVencido($user)) {
            return [
                'inscripcion' => true,
                'renovacion' => false,
                'actualizacion' => false
            ];
        }
        
        // Si está cerca de vencer (7 días): RENOVACIÓN
        if ($this->proveedorProximoAVencer($user)) {
            return [
                'inscripcion' => false,
                'renovacion' => true,
                'actualizacion' => false
            ];
        }
        
        // Si ya es proveedor y está vigente: ACTUALIZACIÓN
        return [
            'inscripcion' => false,
            'renovacion' => false,
            'actualizacion' => true
        ];
    }

    private function proveedorVencido($user)
    {
        // Lógica para verificar si el proveedor está vencido
        if (!$user->proveedor) return true;
        
        // Asumiendo que hay un campo fecha_vencimiento en el modelo proveedor
        // return $user->proveedor->fecha_vencimiento < Carbon::now();
        return false; // Temporal
    }

    private function proveedorProximoAVencer($user)
    {
        // Lógica para verificar si está próximo a vencer (7 días)
        if (!$user->proveedor) return false;
        
        // Asumiendo que hay un campo fecha_vencimiento en el modelo proveedor
        // return $user->proveedor->fecha_vencimiento <= Carbon::now()->addDays(7);
        return false; // Temporal
    }

    private function verificarTramiteEnProgreso($user)
    {
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return null;
        }

        // Buscar un trámite en progreso del solicitante con las relaciones necesarias
        return Tramite::with([
            'detalleTramite.direccion.asentamiento.localidad.municipio.estado'
        ])
        ->where('solicitante_id', $solicitante->id)
        ->whereIn('estado', ['Pendiente', 'En Revision'])
        ->latest()
        ->first();
    }

    public function iniciarInscripcion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'inscripcion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de inscripción
        return $this->crearNuevoTramite('inscripcion', $user);
    }

    public function iniciarRenovacion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'renovacion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de renovación
        return $this->crearNuevoTramite('renovacion', $user);
    }

    public function iniciarActualizacion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'actualizacion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de actualización
        return $this->crearNuevoTramite('actualizacion', $user);
    }

    private function continuarTramite($tramite)
    {
        // Verificar si ya tiene constancia de situación fiscal
        if ($this->tieneConstanciaFiscal($tramite)) {
            // Si ya tiene constancia, ir directamente al formulario principal
            return redirect()->route('tramites.create', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id
            ]);
        }
        
        // Si no tiene constancia, redirigir a la carga de constancia
        return redirect()->route('tramites.solicitante.constancia-fiscal', [
            'tipo_tramite' => strtolower($tramite->tipo_tramite),
            'tramite' => $tramite->id
        ]);
    }

    private function crearNuevoTramite($tipoTramite, $user)
    {
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return back()->with('error', 'No se encontró información del solicitante');
        }

        // Crear nuevo trámite
        $tramite = Tramite::create([
            'solicitante_id' => $solicitante->id,
            'tipo_tramite' => ucfirst($tipoTramite),
            'estado' => 'Pendiente',
            'progreso_tramite' => 0,
            'fecha_inicio' => now(),
        ]);
        
        Log::info('Nuevo trámite creado desde solicitante:', [
            'tramite_id' => $tramite->id,
            'tipo_tramite' => $tipoTramite,
            'solicitante_id' => $solicitante->id
        ]);
        
        // Redirigir a la carga de constancia de situación fiscal
        return redirect()->route('tramites.solicitante.constancia-fiscal', [
            'tipo_tramite' => strtolower($tipoTramite),
            'tramite' => $tramite->id
        ]);
    }

    /**
     * Verifica si el trámite ya tiene constancia de situación fiscal
     */
    private function tieneConstanciaFiscal($tramite)
    {
        // Por ahora solo verificamos si el trámite tiene progreso > 0
        // Más adelante se puede implementar la verificación de documentos
        return $tramite->progreso_tramite > 0;
    }

    /**
     * Muestra el formulario para cargar la constancia de situación fiscal
     */
    public function mostrarConstanciaFiscal($tipoTramite, $tramiteId)
    {
        try {
            $tramite = Tramite::with(['solicitante'])->find($tramiteId);
            
            if (!$tramite) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'Trámite no encontrado');
            }

            // Verificar que el trámite pertenece al usuario actual
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'No tienes permisos para acceder a este trámite');
            }

            return view('tramites.solicitante.constancia-fiscal', [
                'tramite' => $tramite,
                'tipoTramite' => $tipoTramite,
                'solicitante' => $tramite->solicitante
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de constancia fiscal:', [
                'message' => $e->getMessage(),
                'tramite_id' => $tramiteId
            ]);

            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'Error al cargar el formulario de constancia fiscal');
        }
    }

    /**
     * Procesa la carga de la constancia de situación fiscal
     */
    public function subirConstanciaFiscal(Request $request)
    {
        try {
            $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'tipo_tramite' => 'required|string',
                'sat_data' => 'required|string'
            ]);

            $tramite = Tramite::find($request->tramite_id);
            
            if (!$tramite) {
                return back()->with('error', 'Trámite no encontrado');
            }

            // Verificar que el trámite pertenece al usuario actual
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
                return back()->with('error', 'No tienes permisos para este trámite');
            }

            // Decodificar datos del SAT
            $satData = json_decode($request->sat_data, true);
            
            if (!$satData || !isset($satData['details'])) {
                return back()->with('error', 'Los datos de la constancia fiscal no son válidos');
            }

            $details = $satData['details'];

            // Validar que el RFC coincida
            if (isset($details['rfc']) && $details['rfc'] !== $solicitante->rfc) {
                return back()->with('error', 'El RFC de la constancia no coincide con el RFC registrado');
            }

            // Actualizar datos del solicitante si están disponibles
            $updateData = [];
            if (isset($details['razonSocial']) && !empty($details['razonSocial'])) {
                $updateData['razon_social'] = $details['razonSocial'];
            }
            if (isset($details['nombreCompleto']) && !empty($details['nombreCompleto']) && !isset($updateData['razon_social'])) {
                $updateData['razon_social'] = $details['nombreCompleto'];
            }
            if (isset($details['curp']) && !empty($details['curp'])) {
                $updateData['curp'] = $details['curp'];
            }
            if (isset($details['tipoPersona']) && !empty($details['tipoPersona'])) {
                $updateData['tipo_persona'] = ucfirst(strtolower($details['tipoPersona']));
                if ($updateData['tipo_persona'] === 'Fisica') {
                    $updateData['tipo_persona'] = 'Física';
                }
            }

            if (!empty($updateData)) {
                $solicitante->update($updateData);
                Log::info('Datos del solicitante actualizados desde constancia fiscal:', [
                    'solicitante_id' => $solicitante->id,
                    'datos' => $updateData
                ]);
            }

            // Actualizar el progreso del trámite para indicar que ya tiene constancia
            $observaciones = 'Constancia de situación fiscal procesada. RFC: ' . ($details['rfc'] ?? 'N/A');
            if (isset($details['razonSocial'])) {
                $observaciones .= ', Razón Social: ' . $details['razonSocial'];
            }

            $tramite->update([
                'progreso_tramite' => 1,
                'observaciones' => $observaciones
            ]);

            Log::info('Constancia fiscal procesada exitosamente:', [
                'tramite_id' => $tramite->id,
                'rfc' => $details['rfc'] ?? 'N/A',
                'tipo_persona' => $details['tipoPersona'] ?? 'N/A'
            ]);

            // Extraer código postal del SAT si está disponible
            $codigoPostalSat = null;
            if ($request->has('codigo_postal_sat') && !empty($request->codigo_postal_sat)) {
                $codigoPostalSat = $request->codigo_postal_sat;
                Log::info('Código postal del SAT recibido:', ['cp' => $codigoPostalSat]);
            }

            // Redirigir al formulario principal del trámite
            $redirectRoute = redirect()->route('tramites.create', [
                'tipo_tramite' => $request->tipo_tramite,
                'tramite' => $tramite->id
            ])->with('success', 'Constancia de situación fiscal procesada exitosamente. Los datos han sido extraídos automáticamente.');

            // Si hay código postal del SAT, agregarlo a la sesión
            if ($codigoPostalSat) {
                $redirectRoute->with('codigo_postal_sat', $codigoPostalSat);
            }

            return $redirectRoute;

        } catch (\Exception $e) {
            Log::error('Error al procesar constancia fiscal:', [
                'message' => $e->getMessage(),
                'tramite_id' => $request->tramite_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al procesar la constancia fiscal: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los datos de domicilio de un trámite específico
     */
    private function obtenerDatosDomicilio($tramite)
    {
        try {
            // Usar el nuevo método del DetalleTramiteController
            $detalleTramiteController = new DetalleTramiteController();
            $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramite->id);
            
            if ($datosDomicilio) {
                Log::info('🏠 DEBUG DOMICILIO: Datos obtenidos exitosamente usando DetalleTramiteController', [
                    'tramite_id' => $tramite->id,
                    'codigo_postal' => $datosDomicilio['codigo_postal'],
                    'estado' => $datosDomicilio['estado'],
                    'municipio' => $datosDomicilio['municipio']
                ]);
                return $datosDomicilio;
            }
            
            // Si no hay datos, retornar estructura básica
            Log::info('🏠 DEBUG DOMICILIO: No se encontraron datos de domicilio', [
                'tramite_id' => $tramite->id
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('🏠 DEBUG DOMICILIO: Error al obtener datos de domicilio', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
        }
    }

    /**
     * Obtiene los datos del trámite del usuario autenticado
     */
    public function obtenerDatosTramite()
    {
        try {
            $user = Auth::user();
            
            // Buscar el solicitante asociado al usuario
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Buscar el trámite en progreso más reciente
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            $datosTramite = [
                'paso_inicial' => 1,
                'tipo_persona' => $solicitante->tipo_persona,
                'rfc' => $solicitante->rfc,
                'curp' => $solicitante->curp,
                'nombre_completo' => $solicitante->nombre_completo,
                'razon_social' => $solicitante->razon_social,
                'objeto_social' => $solicitante->objeto_social,
                'tramite_id' => null,
                'tipo_tramite' => 'inscripcion'
            ];

            if ($tramite) {
                $datosTramite = [
                    'paso_inicial' => $tramite->progreso_tramite ?? 1,
                    'tipo_persona' => $solicitante->tipo_persona,
                    'rfc' => $solicitante->rfc,
                    'curp' => $solicitante->curp,
                    'nombre_completo' => $solicitante->nombre_completo,
                    'razon_social' => $solicitante->razon_social,
                    'objeto_social' => $solicitante->objeto_social,
                    'tramite_id' => $tramite->id,
                    'tipo_tramite' => strtolower($tramite->tipo_tramite)
                ];
            }

            return response()->json($datosTramite);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los documentos requeridos según el tipo de persona
     */
    public function obtenerDocumentos()
    {
        try {
            $user = Auth::user();
            
            // Obtener el solicitante para determinar el tipo de persona
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tipoPersona = $solicitante->tipo_persona;

            // Obtener documentos según el tipo de persona
            $documentos = Documento::where(function($query) use ($tipoPersona) {
                $query->where('tipo_persona', $tipoPersona)
                      ->orWhere('tipo_persona', 'Ambas');
            })
            ->where('es_visible', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'descripcion', 'tipo_persona']);

            return response()->json([
                'success' => true,
                'documentos' => $documentos,
                'tipo_persona' => $tipoPersona
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sube un documento del trámite
     */
    public function subirDocumento(Request $request)
    {
        try {
            $request->validate([
                'documento' => 'required|file|mimes:pdf|max:10240', // 10MB máximo
                'documento_id' => 'required|integer|exists:documento,id'
            ]);

            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Obtener el trámite en progreso
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un trámite en progreso'
                ], 404);
            }

            $file = $request->file('documento');
            $documentoId = $request->documento_id;

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('doc_' . $documentoId . '_') . '.' . $extension;
            
            // Almacenar archivo
            $ruta = $file->storeAs('documentos_tramite/' . $tramite->id, $nombreArchivo, 'public');

            // Crear o actualizar el registro del documento
            \App\Models\DocumentoSolicitante::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'documento_id' => $documentoId
                ],
                [
                    'fecha_entrega' => now(),
                    'estado' => 'Pendiente',
                    'version_documento' => 1,
                    'ruta_archivo' => $ruta,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Documento subido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finaliza el trámite
     */
    public function finalizarTramite()
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Obtener el trámite en progreso
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un trámite en progreso'
                ], 404);
            }

            // Actualizar el estado del trámite
            $tramite->update([
                'estado' => 'Enviado',
                'fecha_finalizacion' => now(),
                'progreso_tramite' => $solicitante->tipo_persona === 'Física' ? 3 : 6
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite finalizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint para obtener datos de domicilio de un trámite
     */
    public function obtenerDatosDomicilioAPI(Request $request, $tramiteId)
    {
        try {
            $tramite = Tramite::with([
                'detalleTramite.direccion.asentamiento.localidad.municipio.estado'
            ])->find($tramiteId);

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            
            return response()->json([
                'success' => true,
                'datos' => $datosDomicilio,
                'debug' => [
                    'tramite_id' => $tramite->id,
                    'tiene_detalle' => $tramite->detalleTramite ? 'SI' : 'NO',
                    'direccion_id' => $tramite->detalleTramite->direccion_id ?? 'NULL',
                    'tiene_direccion' => ($tramite->detalleTramite && $tramite->detalleTramite->direccion) ? 'SI' : 'NO'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerDatosDomicilioAPI:', [
                'message' => $e->getMessage(),
                'tramite_id' => $tramiteId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
