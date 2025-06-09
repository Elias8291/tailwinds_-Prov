<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Asentamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DireccionController extends Controller
{
    /**
     * Create a new direccion record
     *
     * @param array $data
     * @return Direccion
     */
    public function create(array $data): Direccion
    {
        try {
            // Buscar asentamiento por código postal si está disponible
            $asentamientoId = null;
            if (!empty($data['codigo_postal'])) {
                $asentamiento = Asentamiento::where('codigo_postal', $data['codigo_postal'])->first();
                $asentamientoId = $asentamiento ? $asentamiento->id : null;
            }

            $direccion = Direccion::create([
                'codigo_postal' => intval($data['codigo_postal'] ?? '00000'),
                'asentamiento_id' => $asentamientoId,
                'calle' => $data['calle'] ?? null,
                'numero_exterior' => $data['numero_exterior'] ?? null,
                'numero_interior' => $data['numero_interior'] ?? null,
                'entre_calle_1' => $data['entre_calle_1'] ?? null,
                'entre_calle_2' => $data['entre_calle_2'] ?? null,
            ]);

            Log::info('Dirección creada exitosamente', [
                'direccion_id' => $direccion->id,
                'codigo_postal' => $direccion->codigo_postal
            ]);

            return $direccion;

        } catch (\Exception $e) {
            Log::error('Error al crear dirección', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Error al crear la dirección: ' . $e->getMessage());
        }
    }

    /**
     * Update direccion record
     *
     * @param Direccion $direccion
     * @param array $data
     * @return Direccion
     */
    public function update(Direccion $direccion, array $data): Direccion
    {
        try {
            $direccion->update($data);
            
            Log::info('Dirección actualizada exitosamente', [
                'direccion_id' => $direccion->id
            ]);

            return $direccion;

        } catch (\Exception $e) {
            Log::error('Error al actualizar dirección', [
                'error' => $e->getMessage(),
                'direccion_id' => $direccion->id
            ]);
            throw new \Exception('Error al actualizar la dirección: ' . $e->getMessage());
        }
    }
} 