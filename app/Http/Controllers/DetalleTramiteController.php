<?php

namespace App\Http\Controllers;

use App\Models\DetalleTramite;
use App\Models\Tramite;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
} 