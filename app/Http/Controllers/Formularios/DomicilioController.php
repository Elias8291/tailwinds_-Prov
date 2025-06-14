<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\Asentamiento;
use App\Http\Controllers\DetalleTramiteController;

class DomicilioController extends Controller
{
    /**
     * Guarda los datos de domicilio del trámite
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return bool Retorna true si la operación fue exitosa
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $direccion = $this->saveDireccion($request, $tramite);
        $this->updateDetalleTramite($tramite, $direccion);
        $this->updateProgreso($tramite);

        return true;
    }

    /**
     * Guarda los datos de domicilio desde AJAX
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarDomicilioFormulario ===');
            Log::info('Request completo:', ['data' => $request->all()]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'codigo_postal' => 'required|string|digits:5',
                'colonia' => 'required|integer|exists:asentamiento,id',
                'calle' => 'required|string|max:100',
                'numero_exterior' => 'required|string|max:10',
                'numero_interior' => 'nullable|string|max:10',
                'entre_calle_1' => 'required|string|max:100',
                'entre_calle_2' => 'required|string|max:100',
            ]);

            DB::beginTransaction();

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Guardar los datos usando el método de la clase
            $this->guardar($request, $tramite);

            DB::commit();

            Log::info('Datos de domicilio guardados exitosamente:', [
                'tramite_id' => $tramite->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de domicilio guardados correctamente.',
                'step' => 3
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en domicilio:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda o actualiza los datos de la dirección
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return Direccion El modelo de dirección actualizado
     */
    private function saveDireccion(Request $request, Tramite $tramite)
    {
        $direccionData = [
            'codigo_postal' => $request->input('codigo_postal'),
            'asentamiento_id' => $request->input('colonia'),
            'calle' => $request->input('calle'),
            'numero_exterior' => $request->input('numero_exterior'),
            'numero_interior' => $request->input('numero_interior'),
            'entre_calle_1' => $request->input('entre_calle_1'),
            'entre_calle_2' => $request->input('entre_calle_2'),
        ];

        // Buscar si ya existe una dirección asociada al trámite
        $direccion = null;
        if ($tramite->detalleTramite && $tramite->detalleTramite->direccion_id) {
            $direccion = Direccion::find($tramite->detalleTramite->direccion_id);
        }

        // Si existe la dirección, actualizarla; si no, crear una nueva
        if ($direccion) {
            $direccion->update($direccionData);
            Log::info('Dirección actualizada:', ['direccion_id' => $direccion->id]);
        } else {
            $direccion = Direccion::create($direccionData);
            Log::info('Nueva dirección creada:', ['direccion_id' => $direccion->id]);
        }

        return $direccion;
    }

    /**
     * Actualiza o crea el detalle del trámite con la dirección
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @param Direccion $direccion El modelo de dirección asociado
     * @return void
     */
    private function updateDetalleTramite(Tramite $tramite, Direccion $direccion)
    {
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
        $detalle->direccion_id = $direccion->id;
        $detalle->save();

        Log::info('DetalleTramite actualizado con dirección:', [
            'tramite_id' => $tramite->id,
            'direccion_id' => $direccion->id
        ]);
    }

    /**
     * Actualiza el progreso del trámite
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return void
     */
    private function updateProgreso(Tramite $tramite)
    {
        // Solo actualizar a 3 si el progreso actual es 2
        if ($tramite->progreso_tramite == 2) {
        $tramite->update(['progreso_tramite' => 3]);
        Log::info('Progreso del trámite actualizado:', [
            'tramite_id' => $tramite->id,
            'progreso' => 3
        ]);
        }
    }

    /**
     * Obtiene los datos de domicilio del trámite
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return array Los datos de domicilio formateados
     */
    public function obtenerDatos(Tramite $tramite)
    {
        try {
            Log::info('🏠 DOMICILIO DEBUG: Iniciando obtenerDatos', [
                'tramite_id' => $tramite->id
            ]);

            // Usar el nuevo método del DetalleTramiteController
            $detalleTramiteController = new DetalleTramiteController();
            $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramite->id);
            
            if ($datosDomicilio) {
                Log::info('🏠 DOMICILIO DEBUG: Datos obtenidos exitosamente usando DetalleTramiteController', [
                    'tramite_id' => $tramite->id,
                    'codigo_postal' => $datosDomicilio['codigo_postal'],
                    'estado' => $datosDomicilio['estado'],
                    'municipio' => $datosDomicilio['municipio']
                ]);
                return $datosDomicilio;
            }
            
            // Si no hay datos, retornar estructura básica
            Log::info('🏠 DOMICILIO DEBUG: No se encontraron datos de domicilio', [
                'tramite_id' => $tramite->id
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('🏠 DOMICILIO DEBUG: Error al obtener datos de domicilio', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
        }
    }

    /**
     * Obtiene la información completa de ubicación por código postal
     *
     * @param string $codigoPostal El código postal
     * @return array Los datos de ubicación
     */
    public function obtenerDatosPorCP($codigoPostal)
    {
        try {
            $asentamientos = Asentamiento::where('codigo_postal', $codigoPostal)->get();

            if ($asentamientos->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No se encontraron datos para este código postal'
                ];
            }

            $primerAsentamiento = $asentamientos->first();

            return [
                'success' => true,
                'estado' => $primerAsentamiento->estado,
                'municipio' => $primerAsentamiento->municipio,
                'asentamientos' => $asentamientos->map(function ($asentamiento) {
                    return [
                        'id' => $asentamiento->id,
                        'nombre' => $asentamiento->nombre,
                        'tipo' => $asentamiento->tipo_asentamiento
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos por código postal:', [
                'codigo_postal' => $codigoPostal,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener los datos'
            ];
        }
    }
} 