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
            $request->validate([
                'tramite_id' => 'required|exists:tramite,id',
                'giro' => 'required|string|max:500',
                'sector_id' => 'nullable|exists:sector,id',
                'actividades_seleccionadas' => 'nullable|string',
                'contacto_nombre' => 'required|string|max:40',
                'contacto_cargo' => 'required|string|max:50',
                'contacto_correo' => 'required|email',
                'contacto_telefono' => 'required|string|max:10',
                'pagina_web' => 'nullable|url|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es una petición AJAX, devolver errores de validación como JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors()
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

            // Actualizar progreso del trámite si es la primera vez que completa este paso
            if ($tramite->progreso_tramite < 1) {
                $tramite->progreso_tramite = 1;
                $tramite->save();
            }

            DB::commit();

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
                    'next_step' => 2 // Siguiente paso: domicilio
                ]);
            }

            // Si no es AJAX, redirigir de vuelta a la vista create manteniendo la misma URL
            return redirect()->route('tramites.create', [
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

        $detalle->giro = $request->input('giro');
        $detalle->telefono = $request->input('contacto_telefono');
        $detalle->razon_social = Auth::user()->name;
        $detalle->email = Auth::user()->email;
        $detalle->sitio_web = $request->input('pagina_web');

        $detalle->save();

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
}