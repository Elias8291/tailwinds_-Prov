<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use App\Models\Actividad;
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

    /**
     * Get a specific activity with its sector
     *
     * @param int $actividadId
     * @return JsonResponse
     */
    public function getActividad($actividadId): JsonResponse
    {
        try {
            $actividad = Actividad::with('sector')->findOrFail($actividadId);

            return response()->json([
                'success' => true,
                'data' => $actividad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividad: ' . $e->getMessage()
            ], 500);
        }
    }
} 