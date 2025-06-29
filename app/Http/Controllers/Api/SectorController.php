<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

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
                'message' => 'Error al obtener actividades'
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
            Log::error('Error en getAllActividades: ' . $e->getMessage());
            
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
            
            // Validar query mínimo
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Query demasiado corto',
                    'query' => $query
                ]);
            }

            // Sanitizar query para evitar problemas
            $query = strip_tags($query);
            $query = trim($query);
            
            // Buscar actividades
            $actividades = Actividad::with('sector')
                ->where(function($q) use ($query) {
                    $q->where('nombre', 'LIKE', "%{$query}%")
                      ->orWhere('codigo_scian', 'LIKE', "%{$query}%")
                      ->orWhere('descripcion', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get()
                ->map(function($actividad) {
                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'codigo_scian' => $actividad->codigo_scian,
                        'descripcion' => $actividad->descripcion,
                        'sector' => $actividad->sector ? [
                            'id' => $actividad->sector->id,
                            'nombre' => $actividad->sector->nombre
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $actividades,
                'query' => $query,
                'count' => $actividades->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error en buscarActividades: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
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