<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\ContactoSolicitante;
use App\Models\ActividadSolicitante;
use App\Models\Solicitante;
use App\Models\Sector;
use App\Models\Actividad;

class DatosGeneralesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Método de prueba para verificar que el controlador está accesible
     */
    public function test(Request $request)
    {
        Log::info('=== TEST DatosGeneralesController ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Controlador accesible',
            'timestamp' => now()->toISOString(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Guarda los datos generales del trámite
     */
    public function guardar(Request $request)
    {
        // Log inmediato para confirmar que llegamos al método
        Log::info('🎯 DatosGeneralesController::guardar - MÉTODO ALCANZADO', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ]);

        // Verificar primero que la sesión esté activa
        if (!session()->isStarted()) {
            session()->start();
        }

        Log::info('=== INICIO DatosGeneralesController::guardar ===', [
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'session_started' => session()->isStarted(),
            'session_id' => session()->getId(),
            'request_data' => $request->except(['_token']),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_path' => $request->path(),
            'request_full_url' => $request->fullUrl(),
            'headers' => [
                'content-type' => $request->header('Content-Type'),
                'x-requested-with' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'user-agent' => $request->header('User-Agent')
            ],
            'csrf_token' => $request->input('_token'),
            'csrf_valid' => csrf_token() === $request->input('_token')
        ]);

        // Respuesta inmediata para debug si es AJAX
        if ($request->ajax() || $request->wantsJson()) {
            Log::info('Petición AJAX detectada correctamente');
        }

        try {
            // Validaciones simplificadas para evitar errores innecesarios
            $rules = [
                'tramite_id' => 'required|exists:tramite,id',
                'giro' => [
                    'required',
                    'string',
                    'min:3',
                    'max:500'
                ],
                'actividades_seleccionadas' => 'required|string|min:1',
                'contacto_nombre' => [
                    'required',
                    'string',
                    'min:2',
                    'max:40'
                ],
                'contacto_cargo' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50'
                ],
                'contacto_correo' => [
                    'required',
                    'email',
                    'max:255'
                ],
                'contacto_telefono' => [
                    'required',
                    'string',
                    'min:10',
                    'max:10'
                ],
                'pagina_web' => [
                    'nullable',
                    'string',
                    'max:255'
                ],
            ];

            // Validar nombres específicos según tipo de persona
            $user = Auth::user();
            $solicitante = $user->solicitante ?? null;
            $tipoPersona = $solicitante?->tipo_persona ?? 'Física';

            if ($tipoPersona === 'Física') {
                $rules['nombre_completo'] = [
                    'sometimes',
                    'string',
                    'min:5',
                    'max:255',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ];
                $rules['curp'] = [
                    'sometimes',
                    'string',
                    'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/'
                ];
            } else {
                $rules['razon_social'] = [
                    'sometimes',
                    'string',
                    'min:5',
                    'max:100',
                    'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-\(\)]+$/'
                ];
            }

            // RFC validation if provided
            $rules['rfc'] = [
                'sometimes',
                'string',
                'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/'
            ];

            $messages = [
                // Mensajes de error específicos y elegantes
                'tramite_id.required' => 'Error del sistema: ID de trámite requerido.',
                'tramite_id.exists' => 'Error del sistema: Trámite no válido.',
                
                'giro.required' => 'El giro de la empresa es obligatorio.',
                'giro.min' => 'El giro debe tener al menos 5 caracteres.',
                'giro.max' => 'El giro no puede exceder 500 caracteres.',
                'giro.regex' => 'El giro contiene caracteres no válidos. Use solo letras, números y signos básicos.',
                
                'actividades_seleccionadas.required' => 'Debe seleccionar al menos una actividad económica.',
                'actividades_seleccionadas.min' => 'Debe seleccionar al menos una actividad económica.',
                
                'contacto_nombre.required' => 'El nombre del contacto es obligatorio.',
                'contacto_nombre.min' => 'El nombre debe tener entre 3 y 40 caracteres.',
                'contacto_nombre.max' => 'El nombre debe tener entre 3 y 40 caracteres.',
                'contacto_nombre.regex' => 'El nombre solo puede contener letras y espacios.',
                
                'contacto_cargo.required' => 'El cargo del contacto es obligatorio.',
                'contacto_cargo.min' => 'El cargo debe tener entre 3 y 50 caracteres.',
                'contacto_cargo.max' => 'El cargo debe tener entre 3 y 50 caracteres.',
                'contacto_cargo.regex' => 'El cargo solo puede contener letras y espacios.',
                
                'contacto_correo.required' => 'El correo electrónico del contacto es obligatorio.',
                'contacto_correo.email' => 'Ingrese un correo electrónico válido (ejemplo: usuario@dominio.com).',
                'contacto_correo.max' => 'El correo electrónico es demasiado largo.',
                'contacto_correo.regex' => 'Formato de correo electrónico no válido.',
                
                'contacto_telefono.required' => 'El teléfono del contacto es obligatorio.',
                'contacto_telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
                
                'pagina_web.url' => 'Ingrese una URL válida (ejemplo: https://www.ejemplo.com).',
                'pagina_web.max' => 'La URL es demasiado larga.',
                
                'nombre_completo.min' => 'El nombre completo debe tener entre 5 y 255 caracteres.',
                'nombre_completo.max' => 'El nombre completo debe tener entre 5 y 255 caracteres.',
                'nombre_completo.regex' => 'El nombre completo solo puede contener letras y espacios.',
                
                'razon_social.min' => 'La razón social debe tener entre 5 y 100 caracteres.',
                'razon_social.max' => 'La razón social debe tener entre 5 y 100 caracteres.',
                'razon_social.regex' => 'La razón social contiene caracteres no válidos.',
                
                'curp.regex' => 'CURP inválida. Formato: ABCD123456HDFXYZ12 (18 caracteres).',
                'rfc.regex' => 'RFC inválido. Formato: ABC123456789 (12-13 caracteres).',
            ];

            $request->validate($rules, $messages);

            // Validación adicional para actividades seleccionadas
            $this->validateActividadesSeleccionadas($request);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Errores de validación en datos generales', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'validation_errors' => $e->errors(),
                'is_ajax' => $request->ajax()
            ]);

            // Si es una petición AJAX, devolver errores de validación como JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors(),
                    'debug_info' => [
                        'giro_received' => $request->input('giro'),
                        'giro_length' => strlen($request->input('giro', '')),
                        'all_fields' => $request->except(['_token'])
                    ]
                ], 422);
            }
            
            // Si no es AJAX, lanzar la excepción normal
            throw $e;
        }

        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($request->tramite_id);
            
            // Verificar que el usuario tenga un solicitante asociado
            if (!Auth::check()) {
                throw new \Exception('Usuario no autenticado');
            }
            
            $user = Auth::user();
            $solicitante = $user->solicitante;
            
            // Si el usuario no tiene solicitante, intentar crearlo o buscarlo por RFC del trámite
            if (!$solicitante) {
                Log::warning('Usuario sin solicitante, buscando por trámite', [
                    'user_id' => $user->id,
                    'tramite_id' => $tramite->id
                ]);
                
                // Buscar el solicitante del trámite y asociarlo al usuario si coincide el RFC
                $solicitanteTramite = $tramite->solicitante;
                if ($solicitanteTramite && !$solicitanteTramite->usuario_id) {
                    $solicitanteTramite->usuario_id = $user->id;
                    $solicitanteTramite->save();
                    $solicitante = $solicitanteTramite;
                    
                    Log::info('Solicitante asociado al usuario', [
                        'user_id' => $user->id,
                        'solicitante_id' => $solicitante->id
                    ]);
                } else {
                    throw new \Exception('Usuario sin solicitante asociado y no se puede asociar automáticamente');
                }
            }
            
            // Verificar que el trámite pertenezca al solicitante del usuario
            if ($tramite->solicitante_id != $solicitante->id) {
                Log::error('Trámite no pertenece al usuario', [
                    'user_id' => $user->id,
                    'solicitante_id' => $solicitante->id,
                    'tramite_solicitante_id' => $tramite->solicitante_id
                ]);
                throw new \Exception('No tiene permisos para modificar este trámite');
            }

            // Guardar datos principales
            $detalle = $this->saveDetalleTramite($request, $tramite);
            $this->saveContactoSolicitante($request, $detalle);
            $this->syncActividades($request, $tramite);

            DB::commit();

            // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Avanzar a sección 2: Domicilio
            $tramite->actualizarProgresoSeccion(2);

            Log::info('Datos guardados exitosamente', [
                'tramite_id' => $tramite->id,
                'user_id' => $user->id,
                'action' => $request->action,
                'is_ajax' => $request->ajax()
            ]);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos guardados correctamente',
                    'tramite_id' => $tramite->id,
                    'next_step' => 2, // Siguiente paso: domicilio
                    'progreso_actualizado' => 2
                ]);
            }

            // Si no es AJAX, redirigir de vuelta a la vista create manteniendo la misma URL
            return redirect()->route('tramites.create.tipo', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id
            ])->with('success', 'Datos guardados correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos generales', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);
            
            // Si es una petición AJAX, devolver JSON con error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => 'Error al guardar los datos: ' . $e->getMessage(),
                    'debug_info' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'user_id' => Auth::id(),
                        'timestamp' => now()->toISOString()
                    ]
                ], 500);
            }
            
            // Si es un error de autenticación, redirigir al login
            if (str_contains($e->getMessage(), 'autenticado') || str_contains($e->getMessage(), 'solicitante')) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicie sesión nuevamente.');
            }
            
            return redirect()->back()->with('error', 'Error al guardar los datos: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Obtiene los datos generales del trámite
     */
    public function obtenerDatos(Tramite $tramite)
    {
        Log::info('Obteniendo datos del trámite', [
            'tramite_id' => $tramite->id,
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check()
        ]);

        $solicitante = $tramite->solicitante;
        $detalle = $tramite->detalle;
        $contacto = $detalle?->contacto;
        $actividades = $tramite->actividades()->pluck('actividad_id')->toArray();
        $sector = $tramite->actividades()->with('sector')->first()?->sector;

        return [
            'tramite_id' => $tramite->id,
            'tipo_tramite' => $tramite->tipo_tramite,
            'rfc' => $solicitante->rfc ?? '',
            'tipo_persona' => $solicitante->tipo_persona ?? '',
            'nombre_completo' => Auth::user()->name ?? '',
            'razon_social' => $solicitante->razon_social ?? '',
            'curp' => $solicitante->curp ?? '',
            'giro' => $detalle->giro ?? '',
            'sector_id' => $sector?->id,
            'actividades_seleccionadas' => json_encode($actividades),
            'contacto_nombre' => $contacto->nombre ?? '',
            'contacto_cargo' => $contacto->puesto ?? '',
            'contacto_correo' => $contacto->email ?? '',
            'contacto_telefono' => $contacto->telefono ?? '',
            'pagina_web' => $detalle->sitio_web ?? '',
        ];
    }

    /**
     * Guarda o actualiza los detalles del trámite
     */
    private function saveDetalleTramite(Request $request, Tramite $tramite)
    {
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

        $detalle->giro = $request->input('giro') ?: null;
        $detalle->telefono = $request->input('contacto_telefono') ?: null;
        $detalle->razon_social = Auth::user()->name ?: 'Sin especificar';
        $detalle->email = Auth::user()->email ?: null;
        $detalle->sitio_web = $request->input('pagina_web') ?: null;

        $detalle->save();

        Log::info('DetalleTramite guardado', [
            'tramite_id' => $tramite->id,
            'giro' => $detalle->giro,
            'razon_social' => $detalle->razon_social,
            'telefono' => $detalle->telefono,
            'email' => $detalle->email,
            'sitio_web' => $detalle->sitio_web
        ]);

        return $detalle;
    }

    /**
     * Guarda o actualiza la información del contacto del solicitante
     */
    private function saveContactoSolicitante(Request $request, DetalleTramite $detalle)
    {
        $contacto = ContactoSolicitante::firstOrNew([
            'id' => $detalle->contacto_id
        ]);

        $contacto->nombre = $request->input('contacto_nombre');
        $contacto->puesto = $request->input('contacto_cargo');
        $contacto->telefono = $request->input('contacto_telefono');
        $contacto->email = $request->input('contacto_correo');
        $contacto->save();

        if (!$detalle->contacto_id) {
            $detalle->contacto_id = $contacto->id;
            $detalle->save();
        }
    }

    /**
     * Sincroniza las actividades seleccionadas con el trámite
     */
    private function syncActividades(Request $request, Tramite $tramite)
    {
        // Eliminar actividades existentes
        ActividadSolicitante::where('tramite_id', $tramite->id)->delete();

        $selectedActivities = $this->parseSelectedActivities($request->input('actividades_seleccionadas', ''));

        // Agregar nuevas actividades
        foreach ($selectedActivities as $activityId) {
            if ($activityId) {
                ActividadSolicitante::create([
                    'tramite_id' => $tramite->id,
                    'actividad_id' => $activityId,
                ]);
            }
        }
    }

    /**
     * Convierte las actividades seleccionadas a un array válido
     */
    private function parseSelectedActivities($activities)
    {
        if (is_string($activities)) {
            $decoded = json_decode($activities, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($activities) ? $activities : [];
    }

    /**
     * API para obtener actividades por sector
     */
    public function getActividadesPorSector($sectorId)
    {
        try {
            $actividades = Actividad::where('sector_id', $sectorId)
                ->select('id', 'nombre')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $actividades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividades'
            ], 500);
        }
    }

    /**
     * API para obtener todas las actividades
     */
    public function getAllActividades()
    {
        try {
            $actividades = Actividad::with('sector:id,nombre')
                ->select('id', 'nombre', 'sector_id')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $actividades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividades'
            ], 500);
        }
    }

    /**
     * Muestra los datos generales de un trámite
     */
    public function mostrar(Tramite $tramite)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $solicitante = $user->solicitante;
            
            if (!$solicitante || $tramite->solicitante_id != $solicitante->id) {
                abort(403, 'No tiene permisos para ver este trámite');
            }

            $sectores = Sector::all();
            $actividades = $tramite->actividades()->with('actividad')->get()->pluck('actividad');
            
            $datosTramite = $this->obtenerDatos($tramite);

            return view('revision.datos-generales', compact(
                'tramite',
                'datosTramite', 
                'sectores',
                'actividades'
            ));

        } catch (\Exception $e) {
            Log::error('Error al mostrar datos generales', [
                'tramite_id' => $tramite->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('revision.index')
                ->with('error', 'Error al cargar los datos del trámite');
        }
    }

    /**
     * Valida que se hayan seleccionado actividades válidas
     */
    private function validateActividadesSeleccionadas(Request $request)
    {
        $actividadesSeleccionadas = $request->input('actividades_seleccionadas', '');
        $tieneActividades = false;
        $actividadesValidas = [];

        if (empty($actividadesSeleccionadas) || $actividadesSeleccionadas === '[]' || $actividadesSeleccionadas === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'actividades_seleccionadas' => 'Debe seleccionar al menos una actividad económica.'
            ]);
        }

        try {
            $actividades = json_decode($actividadesSeleccionadas, true);
            
            if (!is_array($actividades)) {
                throw new \Exception('Formato de actividades inválido');
            }

            if (count($actividades) === 0) {
                throw new \Exception('No hay actividades seleccionadas');
            }

            // Verificar que las actividades existan en la base de datos
            foreach ($actividades as $actividadId) {
                if (!empty($actividadId) && is_numeric($actividadId)) {
                    $actividadExiste = Actividad::where('id', $actividadId)->exists();
                    if ($actividadExiste) {
                        $actividadesValidas[] = $actividadId;
                        $tieneActividades = true;
                    } else {
                        Log::warning('Actividad no encontrada en base de datos', [
                            'actividad_id' => $actividadId,
                            'user_id' => Auth::id()
                        ]);
                    }
                }
            }

            if (count($actividadesValidas) === 0) {
                throw new \Exception('Ninguna actividad seleccionada es válida');
            }

            Log::info('Actividades validadas correctamente', [
                'total_enviadas' => count($actividades),
                'actividades_validas' => count($actividadesValidas),
                'ids_validas' => $actividadesValidas,
                'user_id' => Auth::id()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al validar actividades seleccionadas', [
                'actividades_raw' => $actividadesSeleccionadas,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'actividades_seleccionadas' => 'Las actividades seleccionadas no son válidas. Por favor, seleccione actividades de la lista.'
            ]);
        }

        if (!$tieneActividades) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'actividades_seleccionadas' => 'Debe seleccionar al menos una actividad económica válida.'
            ]);
        }
    }
}