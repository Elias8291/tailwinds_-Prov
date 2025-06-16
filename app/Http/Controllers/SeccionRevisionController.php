<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\SeccionRevision;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SeccionRevisionController extends Controller
{
    /**
     * Aprobar una sección específica
     */
    public function aprobarSeccion(Request $request, $tramiteId, $seccionId)
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:1000'
            ]);

            $tramite = Tramite::findOrFail($tramiteId);
            $seccion = SeccionTramite::findOrFail($seccionId);

            // Crear o actualizar la revisión de la sección
            $revision = SeccionRevision::updateOrCreate(
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

            Log::info('Sección aprobada:', [
                'tramite_id' => $tramiteId,
                'seccion_id' => $seccionId,
                'revisor' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sección aprobada correctamente',
                'estado' => 'aprobado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al aprobar sección:', [
                'tramite_id' => $tramiteId,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la sección'
            ], 500);
        }
    }

    /**
     * Rechazar una sección específica
     */
    public function rechazarSeccion(Request $request, $tramiteId, $seccionId)
    {
        try {
            $request->validate([
                'comentario' => 'required|string|max:1000'
            ]);

            $tramite = Tramite::findOrFail($tramiteId);
            $seccion = SeccionTramite::findOrFail($seccionId);

            // Crear o actualizar la revisión de la sección
            $revision = SeccionRevision::updateOrCreate(
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

            Log::info('Sección rechazada:', [
                'tramite_id' => $tramiteId,
                'seccion_id' => $seccionId,
                'revisor' => Auth::id(),
                'comentario' => $request->comentario
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sección rechazada correctamente',
                'estado' => 'rechazado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al rechazar sección:', [
                'tramite_id' => $tramiteId,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la sección'
            ], 500);
        }
    }

    /**
     * Aprobar todo el trámite
     */
    public function aprobarTodo(Request $request, $tramiteId)
    {
        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            
            // Determinar qué secciones aprobar según el tipo de persona
            $seccionesAAprobar = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 6]; // 6 es documentos

            foreach ($seccionesAAprobar as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramiteId,
                        'seccion_id' => $seccionId
                    ],
                    [
                        'estado' => 'aprobado',
                        'comentario' => 'Aprobado en revisión completa',
                        'revisado_por' => Auth::id()
                    ]
                );
            }

            // Actualizar el estado del trámite
            $tramite->update([
                'estado' => 'Aprobado',
                'fecha_revision' => now(),
                'revisado_por' => Auth::id()
            ]);

            DB::commit();

            Log::info('Trámite aprobado completamente:', [
                'tramite_id' => $tramiteId,
                'revisor' => Auth::id(),
                'tipo_persona' => $tipoPersona
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite aprobado completamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al aprobar trámite completo:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el trámite completo'
            ], 500);
        }
    }

    /**
     * Rechazar todo el trámite
     */
    public function rechazarTodo(Request $request, $tramiteId)
    {
        try {
            $request->validate([
                'comentario_general' => 'required|string|max:1000'
            ]);

            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            
            // Determinar qué secciones rechazar según el tipo de persona
            $seccionesARechazar = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 6];

            foreach ($seccionesARechazar as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramiteId,
                        'seccion_id' => $seccionId
                    ],
                    [
                        'estado' => 'rechazado',
                        'comentario' => $request->comentario_general,
                        'revisado_por' => Auth::id()
                    ]
                );
            }

            // Actualizar el estado del trámite
            $tramite->update([
                'estado' => 'Rechazado',
                'fecha_revision' => now(),
                'revisado_por' => Auth::id(),
                'observaciones' => $request->comentario_general
            ]);

            DB::commit();

            Log::info('Trámite rechazado completamente:', [
                'tramite_id' => $tramiteId,
                'revisor' => Auth::id(),
                'comentario' => $request->comentario_general
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite rechazado completamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al rechazar trámite completo:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el trámite completo'
            ], 500);
        }
    }

    /**
     * Pausar la revisión del trámite
     */
    public function pausarRevision(Request $request, $tramiteId)
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:1000'
            ]);

            $tramite = Tramite::findOrFail($tramiteId);

            $tramite->update([
                'estado' => 'Por Cotejar',
                'observaciones' => $request->comentario ?? 'Revisión pausada para cotejo adicional'
            ]);

            Log::info('Revisión pausada:', [
                'tramite_id' => $tramiteId,
                'revisor' => Auth::id(),
                'comentario' => $request->comentario
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Revisión pausada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al pausar revisión:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al pausar la revisión'
            ], 500);
        }
    }

    /**
     * Obtener el estado actual de las revisiones de un trámite
     */
    public function obtenerEstadoRevisiones($tramiteId)
    {
        try {
            $tramite = Tramite::with(['seccionesRevision.seccion'])->findOrFail($tramiteId);
            
            $revisiones = $tramite->seccionesRevision->mapWithKeys(function ($revision) {
                return [$revision->seccion_id => [
                    'estado' => $revision->estado,
                    'comentario' => $revision->comentario,
                    'revisor' => $revision->revisor->name ?? 'N/A',
                    'fecha' => $revision->updated_at->format('d/m/Y H:i')
                ]];
            });

            return response()->json([
                'success' => true,
                'revisiones' => $revisiones
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estado de revisiones:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado de las revisiones'
            ], 500);
        }
    }
} 