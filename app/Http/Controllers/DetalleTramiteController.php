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
            $detalleTramite = $this->findByTramiteId($tramiteId);
            
            if (!$detalleTramite || !$detalleTramite->direccion_id) {
                Log::info('No se encontró detalle de trámite o direccion_id', [
                    'tramite_id' => $tramiteId,
                    'detalle_tramite' => $detalleTramite ? 'encontrado' : 'no encontrado',
                    'direccion_id' => $detalleTramite->direccion_id ?? 'null'
                ]);
                return null;
            }

            // Obtener datos de dirección con información de asentamiento
            $direccionData = DB::table('direccion')
                ->leftJoin('asentamiento', 'direccion.asentamiento_id', '=', 'asentamiento.id')
                ->leftJoin('municipio', 'asentamiento.municipio_id', '=', 'municipio.id')
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
                    'municipio.nombre as municipio',
                    'estado.nombre as estado'
                )
                ->where('direccion.id', $detalleTramite->direccion_id)
                ->first();

            if (!$direccionData) {
                Log::warning('No se encontró información de dirección', [
                    'tramite_id' => $tramiteId,
                    'direccion_id' => $detalleTramite->direccion_id
                ]);
                return null;
            }

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

            Log::info('Datos de domicilio recuperados exitosamente', [
                'tramite_id' => $tramiteId,
                'codigo_postal' => $result['codigo_postal'],
                'estado' => $result['estado'],
                'municipio' => $result['municipio']
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Error al obtener datos de domicilio', [
                'error' => $e->getMessage(),
                'tramite_id' => $tramiteId
            ]);
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
} 