<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\DocumentoSolicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentoSolicitanteController extends Controller
{
    /**
     * Aprobar un documento específico
     */
    public function aprobar(Request $request, $tramiteId, $documentoId)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $documento = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('id', $documentoId)
                ->firstOrFail();

            // Actualizar el estado del documento
            $documento->update([
                'estado' => 'Aprobado',
                'observaciones' => $request->observaciones
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento aprobado exitosamente',
                'data' => [
                    'id' => $documento->id,
                    'estado' => 'Aprobado',
                    'observaciones' => $documento->observaciones,
                    'fecha_revision' => $documento->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar un documento específico
     */
    public function rechazar(Request $request, $tramiteId, $documentoId)
    {
        $request->validate([
            'observaciones' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $tramite = Tramite::findOrFail($tramiteId);
            $documento = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('id', $documentoId)
                ->firstOrFail();

            // Actualizar el estado del documento
            $documento->update([
                'estado' => 'Rechazado',
                'observaciones' => $request->observaciones
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento rechazado exitosamente',
                'data' => [
                    'id' => $documento->id,
                    'estado' => 'Rechazado',
                    'observaciones' => $documento->observaciones,
                    'fecha_revision' => $documento->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el estado de un documento específico
     */
    public function obtenerEstado($tramiteId, $documentoId)
    {
        try {
            $documento = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('id', $documentoId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $documento->id,
                    'estado' => $documento->estado,
                    'observaciones' => $documento->observaciones,
                    'fecha_revision' => $documento->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado del documento: ' . $e->getMessage()
            ], 500);
        }
    }
} 