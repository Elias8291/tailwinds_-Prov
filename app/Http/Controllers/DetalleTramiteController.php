<?php

namespace App\Http\Controllers;

use App\Models\DetalleTramite;
use App\Models\Tramite;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DetalleTramiteController extends Controller
{
    /**
     * Create a new detalle tramite record
     *
     * @param Tramite $tramite
     * @param array $data
     * @return DetalleTramite
     */
    public function create(Tramite $tramite, array $data): DetalleTramite
    {
        try {
            $detalleTramite = DetalleTramite::create([
                'tramite_id' => $tramite->id,
                'razon_social' => $data['razon_social'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? null,
                'direccion_id' => $data['direccion_id'],
                'contacto_id' => $data['contacto_id'] ?? null,
                'representante_legal_id' => $data['representante_legal_id'] ?? null,
                'dato_constitutivo_id' => $data['dato_constitutivo_id'] ?? null,
                'sitio_web' => $data['sitio_web'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Detalle de trámite creado exitosamente', [
                'detalle_tramite_id' => $detalleTramite->id,
                'tramite_id' => $tramite->id,
                'razon_social' => $detalleTramite->razon_social
            ]);

            return $detalleTramite;

        } catch (\Exception $e) {
            Log::error('Error al crear detalle de trámite', [
                'error' => $e->getMessage(),
                'tramite_id' => $tramite->id,
                'data' => $data
            ]);
            throw new \Exception('Error al crear el detalle de trámite: ' . $e->getMessage());
        }
    }

    /**
     * Update detalle tramite record
     *
     * @param DetalleTramite $detalleTramite
     * @param array $data
     * @return DetalleTramite
     */
    public function update(DetalleTramite $detalleTramite, array $data): DetalleTramite
    {
        try {
            $detalleTramite->update($data);
            
            Log::info('Detalle de trámite actualizado exitosamente', [
                'detalle_tramite_id' => $detalleTramite->id
            ]);

            return $detalleTramite;

        } catch (\Exception $e) {
            Log::error('Error al actualizar detalle de trámite', [
                'error' => $e->getMessage(),
                'detalle_tramite_id' => $detalleTramite->id
            ]);
            throw new \Exception('Error al actualizar el detalle de trámite: ' . $e->getMessage());
        }
    }

    /**
     * Find detalle tramite by tramite ID
     *
     * @param int $tramiteId
     * @return DetalleTramite|null
     */
    public function findByTramiteId(int $tramiteId): ?DetalleTramite
    {
        return DetalleTramite::where('tramite_id', $tramiteId)->first();
    }

    /**
     * Get address data including postal code by tramite ID
     *
     * @param int $tramiteId
     * @return array|null
     */
    public function getDatosDomicilioByTramiteId(int $tramiteId): ?array
    {
        try {
            Log::info("🔍 === INICIO getDatosDomicilioByTramiteId ===");
            Log::info("🔍 PASO 1: Tramite ID recibido: {$tramiteId}");
            
            $detalleTramite = $this->findByTramiteId($tramiteId);
            
            Log::info("🔍 PASO 2: Buscando detalle_tramite para tramite_id: {$tramiteId}");
            
            if (!$detalleTramite) {
                Log::warning("❌ PASO 2: No se encontró detalle_tramite para tramite_id: {$tramiteId}");
                // No hay detalle de trámite, retornar estructura básica
                return [
                    'tramite_id' => $tramiteId,
                    'codigo_postal' => null,
                    'estado' => null,
                    'municipio' => null,
                    'colonia' => null,
                    'asentamiento_id' => null,
                    'calle' => null,
                    'numero_exterior' => null,
                    'numero_interior' => null,
                    'entre_calle_1' => null,
                    'entre_calle_2' => null,
                ];
            }
            
            Log::info("✅ PASO 2: detalle_tramite encontrado");
            Log::info("   - detalle_tramite.id: {$detalleTramite->id}");
            Log::info("   - detalle_tramite.tramite_id: {$detalleTramite->tramite_id}");
            Log::info("   - detalle_tramite.direccion_id: " . ($detalleTramite->direccion_id ?? 'NULL'));
            
            if (!$detalleTramite->direccion_id) {
                Log::warning("❌ PASO 3: detalle_tramite no tiene direccion_id asignado");
                // Hay detalle pero no dirección asociada
                return [
                    'tramite_id' => $tramiteId,
                    'codigo_postal' => null,
                    'estado' => null,
                    'municipio' => null,
                    'colonia' => null,
                    'asentamiento_id' => null,
                    'calle' => null,
                    'numero_exterior' => null,
                    'numero_interior' => null,
                    'entre_calle_1' => null,
                    'entre_calle_2' => null,
                ];
            }
            
            Log::info("✅ PASO 3: direccion_id encontrado: {$detalleTramite->direccion_id}");

            // Obtener datos de dirección con información de asentamiento
            Log::info("🔍 PASO 4: Ejecutando consulta SQL para direccion_id: {$detalleTramite->direccion_id}");
            
            $direccionData = DB::table('direccion')
                ->leftJoin('asentamiento', 'direccion.asentamiento_id', '=', 'asentamiento.id')
                ->leftJoin('localidad', 'asentamiento.localidad_id', '=', 'localidad.id')
                ->leftJoin('municipio', 'localidad.municipio_id', '=', 'municipio.id')
                ->leftJoin('estado', 'municipio.estado_id', '=', 'estado.id')
                ->select(
                    'direccion.id as direccion_id',
                    'direccion.codigo_postal',
                    'direccion.calle',
                    'direccion.numero_exterior',
                    'direccion.numero_interior',
                    'direccion.entre_calle_1',
                    'direccion.entre_calle_2',
                    'asentamiento.nombre as colonia',
                    'asentamiento.id as asentamiento_id',
                    'localidad.nombre as localidad',
                    'municipio.nombre as municipio',
                    'estado.nombre as estado'
                )
                ->where('direccion.id', $detalleTramite->direccion_id)
                ->first();

            Log::info("🔍 PASO 5: Resultado de consulta SQL");
            
            if (!$direccionData) {
                Log::warning("❌ PASO 5: No se encontró registro en tabla direccion");
                Log::warning("   - tramite_id: {$tramiteId}");
                Log::warning("   - direccion_id buscado: {$detalleTramite->direccion_id}");
                
                // Verificar si existe el registro en la tabla direccion
                $existeDireccion = DB::table('direccion')->where('id', $detalleTramite->direccion_id)->first();
                if ($existeDireccion) {
                    Log::info("   - ✅ Registro existe en tabla direccion:");
                    Log::info("   - direccion.id: {$existeDireccion->id}");
                    Log::info("   - direccion.codigo_postal: {$existeDireccion->codigo_postal}");
                    Log::info("   - direccion.asentamiento_id: " . ($existeDireccion->asentamiento_id ?? 'NULL'));
                } else {
                    Log::error("   - ❌ No existe registro en tabla direccion con id: {$detalleTramite->direccion_id}");
                }
                
                // Retornar estructura básica en lugar de null
                return [
                    'tramite_id' => $tramiteId,
                    'codigo_postal' => null,
                    'estado' => null,
                    'municipio' => null,
                    'colonia' => null,
                    'asentamiento_id' => null,
                    'calle' => null,
                    'numero_exterior' => null,
                    'numero_interior' => null,
                    'entre_calle_1' => null,
                    'entre_calle_2' => null,
                ];
            }
            
            Log::info("✅ PASO 5: Datos de dirección obtenidos exitosamente");
            Log::info("   - direccion_id: {$direccionData->direccion_id}");
            Log::info("   - codigo_postal: {$direccionData->codigo_postal}");
            Log::info("   - estado: " . ($direccionData->estado ?? 'NULL'));
            Log::info("   - municipio: " . ($direccionData->municipio ?? 'NULL'));
            Log::info("   - colonia: " . ($direccionData->colonia ?? 'NULL'));
            Log::info("   - asentamiento_id: " . ($direccionData->asentamiento_id ?? 'NULL'));

            $result = [
                'tramite_id' => $tramiteId,
                'direccion_id' => $direccionData->direccion_id,
                'codigo_postal' => $direccionData->codigo_postal,
                'estado' => $direccionData->estado,
                'municipio' => $direccionData->municipio,
                'colonia' => $direccionData->colonia,
                'asentamiento_id' => $direccionData->asentamiento_id,
                'calle' => $direccionData->calle,
                'numero_exterior' => $direccionData->numero_exterior,
                'numero_interior' => $direccionData->numero_interior,
                'entre_calle_1' => $direccionData->entre_calle_1,
                'entre_calle_2' => $direccionData->entre_calle_2,
            ];

            Log::info("✅ PASO 6: Array resultado construido exitosamente");
            Log::info("🔍 === RESULTADO FINAL getDatosDomicilioByTramiteId ===");
            Log::info("   - tramite_id: {$result['tramite_id']}");
            Log::info("   - direccion_id: {$result['direccion_id']}");
            Log::info("   - codigo_postal: {$result['codigo_postal']}");
            Log::info("   - estado: " . ($result['estado'] ?? 'NULL'));
            Log::info("   - municipio: " . ($result['municipio'] ?? 'NULL'));
            Log::info("   - colonia: " . ($result['colonia'] ?? 'NULL'));
            Log::info("🔍 === FIN ÉXITO getDatosDomicilioByTramiteId ===");

            return $result;

        } catch (\Exception $e) {
            Log::error("❌ EXCEPCIÓN en getDatosDomicilioByTramiteId:");
            Log::error("   - tramite_id: {$tramiteId}");
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Archivo: " . $e->getFile());
            Log::error("   - Línea: " . $e->getLine());
            Log::error("   - Stack: " . $e->getTraceAsString());
            Log::error("🔍 === FIN ERROR getDatosDomicilioByTramiteId ===");
            return null;
        }
    }

    /**
     * Get postal code by tramite ID
     *
     * @param int $tramiteId
     * @return string|null
     */
    public function getCodigoPostalByTramiteId(int $tramiteId): ?string
    {
        $datosDomicilio = $this->getDatosDomicilioByTramiteId($tramiteId);
        return $datosDomicilio ? $datosDomicilio['codigo_postal'] : null;
    }

    /**
     * API endpoint to get domicilio data by tramite ID
     *
     * @param int $tramiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomicilioApi(int $tramiteId)
    {
        try {
            Log::info("🌐 === INICIO getDomicilioApi ===");
            Log::info("🌐 API: Tramite ID recibido: {$tramiteId}");
            
            $datosDomicilio = $this->getDatosDomicilioByTramiteId($tramiteId);
            
            Log::info("🌐 API: Datos obtenidos de getDatosDomicilioByTramiteId:");
            Log::info("   - Resultado es null: " . ($datosDomicilio === null ? 'SÍ' : 'NO'));
            
            if ($datosDomicilio) {
                Log::info("   - codigo_postal: " . ($datosDomicilio['codigo_postal'] ?? 'NULL'));
                Log::info("   - estado: " . ($datosDomicilio['estado'] ?? 'NULL'));
                Log::info("   - municipio: " . ($datosDomicilio['municipio'] ?? 'NULL'));
            }
            
            if ($datosDomicilio && $datosDomicilio['codigo_postal']) {
                Log::info("✅ API: Retornando success=true con datos completos");
                // Hay datos completos de domicilio
                return response()->json([
                    'success' => true,
                    'domicilio' => $datosDomicilio
                ]);
            } else if ($datosDomicilio) {
                Log::info("⚠️ API: Retornando success=false - datos incompletos");
                // Hay estructura pero sin datos completos
                return response()->json([
                    'success' => false,
                    'message' => 'El trámite no tiene datos de domicilio completos',
                    'domicilio' => $datosDomicilio
                ]);
            } else {
                Log::info("❌ API: Retornando success=false - sin datos");
                // No hay datos en absoluto
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos de domicilio para este trámite',
                    'domicilio' => [
                        'tramite_id' => $tramiteId,
                        'codigo_postal' => null,
                        'estado' => null,
                        'municipio' => null,
                        'colonia' => null,
                        'asentamiento_id' => null,
                        'calle' => null,
                        'numero_exterior' => null,
                        'numero_interior' => null,
                        'entre_calle_1' => null,
                        'entre_calle_2' => null,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ EXCEPCIÓN en API getDomicilioApi:");
            Log::error("   - tramite_id: {$tramiteId}");
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Stack: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de domicilio',
                'domicilio' => null
            ], 500);
        }
    }
} 