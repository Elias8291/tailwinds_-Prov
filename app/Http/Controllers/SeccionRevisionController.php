<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\SeccionRevision;
use App\Models\SeccionTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeccionRevisionController extends Controller
{
    /**
     * Aprobar una sección de trámite
     */
    public function aprobar(Request $request, $tramiteId, $seccionId)
    {
        $request->validate([
            'comentario' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $seccion = SeccionTramite::findOrFail($seccionId);

            // Crear o actualizar la revisión de la sección
            $seccionRevision = SeccionRevision::updateOrCreate(
                [
                    'tramite_id' => $tramiteId,
                    'seccion_id' => $seccionId
                ],
                [
                    'estado' => 'aprobado',
                    'comentario' => $request->comentario,
                    'revisado_por' => Auth::id()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sección aprobada exitosamente',
                'data' => [
                    'estado' => 'aprobado',
                    'comentario' => $seccionRevision->comentario,
                    'revisado_por' => $seccionRevision->revisadoPor?->name ?? 'Usuario',
                    'fecha_revision' => $seccionRevision->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la sección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar una sección de trámite
     */
    public function rechazar(Request $request, $tramiteId, $seccionId)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $seccion = SeccionTramite::findOrFail($seccionId);

            // Crear o actualizar la revisión de la sección
            $seccionRevision = SeccionRevision::updateOrCreate(
                [
                    'tramite_id' => $tramiteId,
                    'seccion_id' => $seccionId
                ],
                [
                    'estado' => 'rechazado',
                    'comentario' => $request->comentario,
                    'revisado_por' => Auth::id()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sección rechazada exitosamente',
                'data' => [
                    'estado' => 'rechazado',
                    'comentario' => $seccionRevision->comentario,
                    'revisado_por' => $seccionRevision->revisadoPor?->name ?? 'Usuario',
                    'fecha_revision' => $seccionRevision->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la sección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el estado de revisión de una sección
     */
    public function obtenerEstado($tramiteId, $seccionId)
    {
        try {
            $seccionRevision = SeccionRevision::where('tramite_id', $tramiteId)
                ->where('seccion_id', $seccionId)
                ->with('revisadoPor')
                ->first();

            if (!$seccionRevision) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'estado' => 'pendiente',
                        'comentario' => null,
                        'revisado_por' => null,
                        'fecha_revision' => null
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'estado' => $seccionRevision->estado,
                    'comentario' => $seccionRevision->comentario,
                    'revisado_por' => $seccionRevision->revisadoPor?->name ?? 'Usuario',
                    'fecha_revision' => $seccionRevision->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado: ' . $e->getMessage()
            ], 500);
        }
    }
} 
