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
     * Guarda los datos generales del trámite
     */
    public function guardar(Request $request)
    {
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
            'request_url' => $request->url()
        ]);

        try {
            // Validaciones personalizadas según las reglas del JavaScript
            $request->validate([
                'tramite_id' => 'required|exists:tramite,id',
                'giro' => [
                    'required',
                    'string',
                    'min:10',
                    'max:500',
                    'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-\(\)]+$/'
                ],
                'sector_id' => 'nullable|exists:sector,id',
                'actividades_seleccionadas' => 'nullable|string',
                'contacto_nombre' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'contacto_cargo' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'contacto_correo' => [
                    'required',
                    'email',
                    'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/'
                ],
                'contacto_telefono' => [
                    'required',
                    'string',
                    'regex:/^\d{10}$/'
                ],
                'pagina_web' => [
                    'nullable',
                    'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
                ],
            ], [
                // Mensajes personalizados que coinciden con el JavaScript
                'giro.required' => 'El giro es obligatorio.',
                'giro.min' => 'El giro debe tener entre 10 y 500 caracteres. Solo se permiten letras, números y signos básicos.',
                'giro.max' => 'El giro debe tener entre 10 y 500 caracteres. Solo se permiten letras, números y signos básicos.',
                'giro.regex' => 'El giro debe tener entre 10 y 500 caracteres. Solo se permiten letras, números y signos básicos.',
                'contacto_nombre.required' => 'El nombre es obligatorio.',
                'contacto_nombre.min' => 'El nombre debe tener entre 2 y 100 caracteres. Solo se permiten letras y espacios.',
                'contacto_nombre.max' => 'El nombre debe tener entre 2 y 100 caracteres. Solo se permiten letras y espacios.',
                'contacto_nombre.regex' => 'El nombre debe tener entre 2 y 100 caracteres. Solo se permiten letras y espacios.',
                'contacto_cargo.required' => 'El cargo es obligatorio.',
                'contacto_cargo.min' => 'El cargo debe tener entre 2 y 50 caracteres. Solo se permiten letras y espacios.',
                'contacto_cargo.max' => 'El cargo debe tener entre 2 y 50 caracteres. Solo se permiten letras y espacios.',
                'contacto_cargo.regex' => 'El cargo debe tener entre 2 y 50 caracteres. Solo se permiten letras y espacios.',
                'contacto_correo.required' => 'El correo electrónico es obligatorio.',
                'contacto_correo.email' => 'Ingrese un correo electrónico válido.',
                'contacto_correo.regex' => 'Ingrese un correo electrónico válido.',
                'contacto_telefono.required' => 'El teléfono es obligatorio.',
                'contacto_telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
                'pagina_web.regex' => 'Ingrese una URL válida (ej: https://www.ejemplo.com)',
            ]);

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
                'is_ajax' => $request->ajax()
            ]);
            
            // Si es una petición AJAX, devolver JSON con error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => 'Error al guardar los datos: ' . $e->getMessage()
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
     * Valida que se hayan seleccionado actividades (similar al JavaScript)
     */
    private function validateActividadesSeleccionadas(Request $request)
    {
        $actividadesSeleccionadas = $request->input('actividades_seleccionadas', '');
        $tieneActividades = false;

        if (!empty($actividadesSeleccionadas)) {
            try {
                $actividades = json_decode($actividadesSeleccionadas, true);
                if (is_array($actividades) && count($actividades) > 0) {
                    // Verificar que al menos una actividad no esté vacía
                    foreach ($actividades as $actividad) {
                        if (!empty($actividad)) {
                            $tieneActividades = true;
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Si no se puede decodificar el JSON, asumir que no hay actividades
                $tieneActividades = false;
            }
        }

        if (!$tieneActividades) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'actividades_seleccionadas' => 'Debe seleccionar al menos una actividad.'
            ]);
        }
    }
}