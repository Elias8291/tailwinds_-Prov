<?php

namespace App\Http\Controllers;

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
    /**
     * Obtiene los datos generales de un trámite por su ID
     *
     * @param int $tramiteId ID del trámite
     * @return array Datos generales del trámite
     */
    public function obtenerDatos($tramiteId)
    {
        try {
            $tramite = Tramite::with([
                'solicitante',
                'detalleTramite.contacto',
                'actividades'
            ])->find($tramiteId);

            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            $solicitante = $tramite->solicitante;
            $detalleTramite = $tramite->detalleTramite;
            $contacto = $detalleTramite ? $detalleTramite->contacto : null;

            // Obtener actividades seleccionadas
            $actividadesSeleccionadas = $tramite->actividades()->pluck('actividad_id')->toArray();

            return [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'rfc' => $solicitante->rfc ?? '',
                'tipo_persona' => $solicitante->tipo_persona ?? 'Física',
                'curp' => $solicitante->curp ?? '',
                'nombre_completo' => $solicitante->nombre_completo ?? '',
                'razon_social' => $solicitante->razon_social ?? $detalleTramite->razon_social ?? '',
                'objeto_social' => $solicitante->objeto_social ?? '',
                'sector_id' => $tramite->sector_id ?? null,
                'actividades' => $actividadesSeleccionadas,
                'actividades_seleccionadas' => json_encode($actividadesSeleccionadas),
                'contacto_nombre' => $contacto->nombre ?? '',
                'contacto_cargo' => $contacto->puesto ?? '',
                'contacto_correo' => $contacto->email ?? '',
                'contacto_telefono' => $contacto->telefono ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos generales:', [
                'tramite_id' => $tramiteId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Obtiene los datos generales de un trámite por su ID (endpoint AJAX)
     *
     * @param Request $request
     * @param int $tramiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerDatosAjax(Request $request, $tramiteId)
    {
        try {
            $datos = $this->obtenerDatos($tramiteId);
            
            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para el trámite especificado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $datos
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerDatosAjax:', [
                'tramite_id' => $tramiteId,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda los datos generales de un trámite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function guardarDatos(Request $request)
    {
        try {
            Log::info('=== INICIO guardarDatos DatosGeneralesController ===');
            Log::info('Request completo:', ['data' => $request->all()]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'tipo_persona' => 'required|in:Física,Moral',
                'rfc' => 'required|string|max:13',
                'curp' => 'nullable|string|max:18',
                'nombre_completo' => 'nullable|string|max:255',
                'razon_social' => 'required|string|max:100',
                'objeto_social' => 'required|string|max:500',
                'sector_id' => 'nullable|integer|exists:sector,id',
                'actividades_seleccionadas' => 'nullable|string',
                'contacto_nombre' => 'required|string|max:40',
                'contacto_cargo' => 'required|string|max:50',
                'contacto_correo' => 'required|email|max:255',
                'contacto_telefono' => 'required|string|max:10',
            ]);

            DB::beginTransaction();

            // Buscar el trámite
            $tramite = Tramite::with('solicitante')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Actualizar datos del solicitante
            $solicitante = $tramite->solicitante;
            if ($solicitante) {
                $solicitante->update([
                    'tipo_persona' => $validated['tipo_persona'],
                    'rfc' => $validated['rfc'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'nombre_completo' => $validated['nombre_completo'],
                    'razon_social' => $validated['razon_social'],
                    'objeto_social' => $validated['objeto_social'],
                ]);
            } else {
                // Crear solicitante si no existe
                $solicitante = Solicitante::create([
                    'rfc' => $validated['rfc'],
                    'tipo_persona' => $validated['tipo_persona'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'nombre_completo' => $validated['nombre_completo'],
                    'razon_social' => $validated['razon_social'],
                    'objeto_social' => $validated['objeto_social'],
                ]);
                
                $tramite->update(['solicitante_id' => $solicitante->id]);
            }

            // Crear o actualizar el contacto
            $contacto = ContactoSolicitante::create([
                'nombre' => $validated['contacto_nombre'],
                'puesto' => $validated['contacto_cargo'],
                'telefono' => $validated['contacto_telefono'],
                'email' => $validated['contacto_correo'],
            ]);

            // Actualizar detalles del trámite
            $detalleTramite = DetalleTramite::updateOrCreate(
                ['tramite_id' => $tramite->id],
                [
                    'razon_social' => $validated['razon_social'],
                    'email' => $validated['contacto_correo'],
                    'telefono' => $validated['contacto_telefono'],
                    'contacto_id' => $contacto->id,
                ]
            );

            // Actualizar sector del trámite
            if (!empty($validated['sector_id'])) {
                $tramite->update(['sector_id' => $validated['sector_id']]);
            }

            // Procesar actividades seleccionadas
            if (!empty($validated['actividades_seleccionadas'])) {
                $actividades = json_decode($validated['actividades_seleccionadas'], true);
                if (!empty($actividades)) {
                    // Eliminar actividades previas del trámite
                    ActividadSolicitante::where('tramite_id', $tramite->id)->delete();
                    
                    // Crear nuevas relaciones de actividades
                    foreach ($actividades as $actividadId) {
                        ActividadSolicitante::create([
                            'tramite_id' => $tramite->id,
                            'actividad_id' => $actividadId,
                        ]);
                    }
                }
            }

            // Actualizar progreso del trámite
            if ($tramite->progreso_tramite < 2) {
                $tramite->update(['progreso_tramite' => 2]);
            }

            DB::commit();

            Log::info('Datos generales guardados exitosamente:', [
                'tramite_id' => $tramite->id,
                'solicitante_id' => $solicitante->id
            ]);

            // Respuesta para AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos generales guardados correctamente',
                    'data' => $this->obtenerDatos($tramite->id)
                ]);
            }

            // Respuesta para formulario normal
            return redirect()->route('tramites.create', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id,
                'step' => 2
            ])->with('success', 'Datos generales guardados correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en datos generales:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos generales:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar los datos: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al guardar los datos: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Obtiene los sectores disponibles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerSectores()
    {
        try {
            $sectores = Sector::select('id', 'nombre')->orderBy('nombre')->get();
            
            return response()->json([
                'success' => true,
                'data' => $sectores
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener sectores:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los sectores'
            ], 500);
        }
    }

    /**
     * Obtiene las actividades de un sector
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerActividades(Request $request)
    {
        try {
            $sectorId = $request->get('sector_id');
            
            if (!$sectorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del sector requerido'
                ], 400);
            }

            $actividades = Actividad::where('sector_id', $sectorId)
                ->select('id', 'nombre')
                ->orderBy('nombre')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $actividades
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener actividades:', [
                'sector_id' => $request->get('sector_id'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las actividades'
            ], 500);
        }
    }

    /**
     * Muestra el formulario de datos generales para un trámite específico
     *
     * @param int $tramiteId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function mostrar($tramiteId)
    {
        try {
            $datos = $this->obtenerDatos($tramiteId);
            
            if (empty($datos)) {
                return redirect()->route('tramites.index')
                    ->with('error', 'No se encontraron datos para el trámite especificado');
            }

            $tramite = Tramite::with('solicitante')->find($tramiteId);
            $sectores = Sector::all();
            
            return view('datos-generales.formulario', [
                'datosTramite' => $datos,
                'tramite' => $tramite,
                'sectores' => $sectores,
                'datosSolicitante' => [
                    'rfc' => $datos['rfc'],
                    'tipo_persona' => $datos['tipo_persona'],
                    'curp' => $datos['curp'],
                    'nombre_completo' => $datos['nombre_completo'],
                    'razon_social' => $datos['razon_social'],
                    'objeto_social' => $datos['objeto_social']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de datos generales:', [
                'tramite_id' => $tramiteId,
                'message' => $e->getMessage()
            ]);

            return redirect()->route('tramites.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de datos generales para revisión
     *
     * @param int $tramiteId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function mostrarRevision($tramiteId)
    {
        try {
            $datos = $this->obtenerDatos($tramiteId);
            
            if (empty($datos)) {
                return redirect()->route('revision.index')
                    ->with('error', 'No se encontraron datos para el trámite especificado');
            }

            $tramite = Tramite::with('solicitante')->find($tramiteId);
            $sectores = Sector::all();
            $actividades = [];
            
            if (!empty($datos['sector_id'])) {
                $actividades = Actividad::where('sector_id', $datos['sector_id'])->get();
            }

            return view('revision.datos-generales', [
                'datosTramite' => $datos,
                'tramite' => $tramite,
                'sectores' => $sectores,
                'actividades' => $actividades,
                'modoRevision' => true,
                'datosSolicitante' => [
                    'rfc' => $datos['rfc'],
                    'tipo_persona' => $datos['tipo_persona'],
                    'curp' => $datos['curp'],
                    'nombre_completo' => $datos['nombre_completo'],
                    'razon_social' => $datos['razon_social'],
                    'objeto_social' => $datos['objeto_social']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de revisión:', [
                'tramite_id' => $tramiteId,
                'message' => $e->getMessage()
            ]);

            return redirect()->route('revision.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Prepara los datos para revisión de un trámite
     *
     * @param int $tramiteId
     * @return array
     */
    public function prepararDatosRevision($tramiteId)
    {
        try {
            $datos = $this->obtenerDatos($tramiteId);
            
            if (empty($datos)) {
                return [];
            }

            // Agregar información adicional para revisión
            $tramite = Tramite::with('solicitante')->find($tramiteId);
            $sectores = Sector::all();
            $actividades = [];
            
            if (!empty($datos['sector_id'])) {
                $actividades = Actividad::where('sector_id', $datos['sector_id'])->get();
            }

            return [
                'datosTramite' => $datos,
                'tramite' => $tramite,
                'sectores' => $sectores,
                'actividades' => $actividades,
                'modoRevision' => true
            ];

        } catch (\Exception $e) {
            Log::error('Error al preparar datos para revisión:', [
                'tramite_id' => $tramiteId,
                'message' => $e->getMessage()
            ]);

            return [];
        }
    }
} 