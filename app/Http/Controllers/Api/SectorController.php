<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectorController extends Controller
{
    /**
     * Get activities for a specific sector
     *
     * @param int $sectorId
     * @return JsonResponse
     */
    public function getActividades($sectorId): JsonResponse
    {
        try {
            $sector = Sector::findOrFail($sectorId);
            $actividades = $sector->actividades;

            return response()->json([
                'success' => true,
                'data' => $actividades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividades: ' . $e->getMessage()
            ], 500);
        }
    }
} 