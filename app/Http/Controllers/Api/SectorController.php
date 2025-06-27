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
     * Get all activities (simplified without sectors)
     *
     * @return JsonResponse
     */
    public function getAllActividades(): JsonResponse
    {
        try {
            $actividades = Actividad::select(['id', 'nombre', 'codigo_scian', 'descripcion', 'fuente'])
                ->orderBy('nombre')
                ->get()
                ->map(function ($actividad) {
                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'codigo' => $actividad->codigo_scian ?? "ACT-{$actividad->id}",
                        'codigo_scian' => $actividad->codigo_scian,
                        'descripcion' => $actividad->descripcion,
                        'fuente' => $actividad->fuente ?? 'MANUAL'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $actividades,
                'total' => $actividades->count(),
                'fuentes' => $actividades->groupBy('fuente')->map->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getAllActividades: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search activities by name or SCIAN code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function buscarActividades(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $limit = (int) $request->get('limit', 50);
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Query demasiado corto'
                ]);
            }

            // Búsqueda con sector incluido
            $actividades = Actividad::with('sector')
                ->where(function ($q) use ($query) {
                    $q->where('nombre', 'LIKE', "%{$query}%")
                      ->orWhere('codigo_scian', 'LIKE', "%{$query}%");
                    
                    // Solo buscar en descripción si existe
                    if (!empty($query)) {
                        $q->orWhere('descripcion', 'LIKE', "%{$query}%");
                    }
                })
                ->orderBy('nombre')
                ->limit($limit)
                ->get();

            // Procesar los resultados para devolver solo los datos necesarios
            $actividades = $actividades->map(function ($actividad) {
                return [
                    'id' => $actividad->id,
                    'nombre' => $actividad->nombre,
                    'sector' => $actividad->sector ? $actividad->sector->nombre : 'Sin sector',
                    'sector_id' => $actividad->sector_id,
                    'codigo_scian' => $actividad->codigo_scian,
                    'descripcion' => $actividad->descripcion,
                    'fuente' => $actividad->fuente ?? 'MANUAL'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $actividades,
                'query' => $query,
                'total' => $actividades->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en buscarActividades: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en búsqueda: ' . $e->getMessage(),
                'debug' => [
                    'query' => $query ?? 'undefined',
                    'limit' => $limit ?? 'undefined',
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
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
    {
        try {
            $query = $request->get('q', '');
            $limit = $request->get('limit', 50);
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Query demasiado corto'
                ]);
            }

            $actividades = Actividad::with('sector')
                ->where(function ($q) use ($query) {
                    $q->where('nombre', 'LIKE', "%{$query}%")
                      ->orWhere('codigo_scian', 'LIKE', "%{$query}%")
                      ->orWhere('descripcion', 'LIKE', "%{$query}%")
                      ->orWhereHas('sector', function ($sq) use ($query) {
                          $sq->where('nombre', 'LIKE', "%{$query}%");
                      });
                })
                ->orderByRaw("
                    CASE 
                        WHEN nombre LIKE ? THEN 1
                        WHEN codigo_scian LIKE ? THEN 2
                        WHEN descripcion LIKE ? THEN 3
                        ELSE 4
                    END
                ", ["{$query}%", "{$query}%", "%{$query}%"])
                ->orderBy('nombre')
                ->limit($limit)
                ->get()
                ->map(function ($actividad) {
                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'codigo' => $actividad->codigo_scian ?? $actividad->id,
                        'codigo_scian' => $actividad->codigo_scian,
                        'descripcion' => $actividad->descripcion,
                        'fuente' => $actividad->fuente ?? 'MANUAL',
                        'sector' => [
                            'id' => $actividad->sector->id ?? null,
                            'nombre' => $actividad->sector->nombre ?? 'Sin sector',
                            'codigo' => $actividad->sector->codigo ?? null
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $actividades,
                'query' => $query,
                'total' => $actividades->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en búsqueda: ' . $e->getMessage()
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