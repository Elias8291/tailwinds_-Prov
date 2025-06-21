<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Support\Facades\DB;

class DocumentosController extends Controller
{
    /**
     * Sube un documento para un trámite pendiente
     *
     * @param Request $request La solicitud con los datos del documento
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación
     */
    public function subir(Request $request)
    {
        $this->validateRequest($request, 'subir');

        $solicitante = Auth::user()->solicitante;
        
        Log::info('Intentando subir documento', [
            'user_id' => Auth::id(),
            'solicitante_id' => $solicitante->id,
            'documento_id' => $request->input('documento_id')
        ]);
        
        $tramite = $this->getTramitePendiente($solicitante->id);
        
        if ($tramite) {
            Log::info('Trámite encontrado', [
                'tramite_id' => $tramite->id,
                'estado' => $tramite->estado,
                'progreso' => $tramite->progreso_tramite
            ]);
        } else {
            Log::warning('No se encontró trámite activo', [
                'solicitante_id' => $solicitante->id
            ]);
        }

        if (!$tramite) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró un trámite activo. Solo se pueden subir documentos a trámites en estado Pendiente o En Revisión.'
            ], 400);
        }

        return DB::transaction(function () use ($request, $tramite) {
            $documento = Documento::find($request->input('documento_id'));
            $ruta = $this->storeArchivo($request->file('archivo'), $tramite->id, $documento->id);
            $docSolicitante = $this->guardarDocumentoSolicitante($tramite->id, $documento->id, $ruta);

            // Verificar si se han subido todos los documentos requeridos
            $this->verificarYActualizarProgreso($tramite);

            return response()->json([
                'success' => true,
                'ruta' => asset('storage/' . $ruta),
                'docSolicitanteId' => $docSolicitante->id,
                'mensaje' => 'Documento subido correctamente',
            ]);
        });
    }

    /**
     * Obtiene los documentos asociados a un trámite
     *
     * @param Request $request La solicitud HTTP
     * @param int $tramiteId El ID del trámite
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los documentos
     */
    public function get(Request $request, $tramiteId)
    {
        $this->validateRequest($request, 'get', $tramiteId);

        $documentos = DocumentoSolicitante::where('tramite_id', $tramiteId)
            ->with('documento')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'documento_id' => $doc->documento_id,
                'nombre' => $doc->documento->nombre,
                'tipo' => $doc->documento->tipo,
                'fecha_entrega' => $doc->fecha_entrega
                    ? \Carbon\Carbon::parse($doc->fecha_entrega)->toIso8601String()
                    : null,
                'estado' => $doc->estado,
                'version_documento' => $doc->version_documento,
                'ruta_archivo' => asset('storage/' . Crypt::decryptString($doc->ruta_archivo)),
            ])
            ->toArray();

        return response()->json([
            'success' => true,
            'documentos' => $documentos,
            'mensaje' => 'Documentos obtenidos correctamente.',
        ]);
    }

    /**
     * Valida los datos de la solicitud
     *
     * @param Request $request La solicitud a validar
     * @param string $method El método que se está ejecutando (subir o get)
     * @param int|null $tramiteId El ID del trámite, si aplica
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request, string $method, $tramiteId = null)
    {
        $rules = $method === 'subir'
            ? [
                'documento_id' => 'required|exists:documento,id',
                'archivo' => 'required|file|mimes:pdf|max:10240',
            ]
            : [
                'tramiteId' => 'required|exists:tramite,id',
            ];

        if ($method === 'update') {
            $rules['tramiteId'] = 'required|exists:tramite,id';
            $rules['documento_id'] = 'required|exists:documento,id';
        }

        $request->merge(['tramiteId' => $tramiteId]);
        $request->validate($rules);
    }

    /**
     * Obtiene el trámite activo para un solicitante (Pendiente o En Revision)
     *
     * @param int $solicitanteId El ID del solicitante
     * @return Tramite|null El trámite activo o null si no se encuentra
     */
    private function getTramitePendiente($solicitanteId): ?Tramite
    {
        return Tramite::where('solicitante_id', $solicitanteId)
            ->whereIn('estado', ['Pendiente', 'En Revision'])
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * Almacena el archivo en el sistema de almacenamiento
     *
     * @param \Illuminate\Http\UploadedFile $archivo El archivo subido
     * @param int $tramiteId El ID del trámite
     * @param int $documentoId El ID del documento
     * @return string La ruta donde se almacenó el archivo
     */
    private function storeArchivo($archivo, $tramiteId, $documentoId): string
    {
        $nombreArchivo = uniqid('doc_' . $documentoId . '_') . '.pdf';
        return $archivo->storeAs('documentos_solicitante/' . $tramiteId, $nombreArchivo, 'public');
    }

    /**
     * Guarda o actualiza el registro del documento del solicitante
     *
     * @param int $tramiteId El ID del trámite
     * @param int $documentoId El ID del documento
     * @param string $ruta La ruta del archivo almacenado
     * @return DocumentoSolicitante El registro del documento del solicitante
     */
    private function guardarDocumentoSolicitante($tramiteId, $documentoId, $ruta): DocumentoSolicitante
    {
        return DocumentoSolicitante::updateOrCreate(
            [
                'tramite_id' => $tramiteId,
                'documento_id' => $documentoId,
            ],
            [
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => DocumentoSolicitante::where('tramite_id', $tramiteId)
                    ->where('documento_id', $documentoId)
                    ->max('version_documento') + 1,
                'ruta_archivo' => Crypt::encryptString($ruta),
                'observaciones' => null, // Limpiar observaciones al subir nuevo documento
            ]
        );
    }

    public function updateDocumentStatus(Request $request, $tramiteId, $documentoId)
    {
        $request->validate([
            'approval' => 'required|in:approved,not-approved',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            Log::info('Updating document status:', [
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval'),
                'comment' => $request->input('comment')
            ]);

            $docSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->firstOrFail();
            $docSolicitante->estado = $request->input('approval') === 'approved' ? 'Aprobado' : 'Rechazado';
            $docSolicitante->observaciones = $request->input('comment');
            $docSolicitante->save();

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento actualizado correctamente.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Document not found:', ['tramiteId' => $tramiteId, 'documentoId' => $documentoId]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Documento no encontrado.',
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error updating document status:', [
                'message' => $e->getMessage(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval')
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating document status:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval')
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Error inesperado al actualizar el documento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verifica si se han subido todos los documentos requeridos y actualiza el progreso
     *
     * @param Tramite $tramite El trámite asociado
     * @return void
     */
    private function verificarYActualizarProgreso(Tramite $tramite)
    {
        $tipoPersona = $tramite->solicitante->tipo_persona;
        
        // Obtener total de documentos requeridos para el tipo de persona
        $totalDocumentosRequeridos = Documento::where(function($query) use ($tipoPersona) {
            $query->where('tipo_persona', $tipoPersona)
                  ->orWhere('tipo_persona', 'Ambas');
        })
        ->where('es_visible', true)
        ->count();
        
        // Obtener documentos ya subidos para este trámite del tipo de persona
        $documentosSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
            ->whereHas('documento', function($query) use ($tipoPersona) {
                $query->where('es_visible', true)
                      ->where(function($subQuery) use ($tipoPersona) {
                          $subQuery->where('tipo_persona', $tipoPersona)
                                   ->orWhere('tipo_persona', 'Ambas');
                      });
            })
            ->count();

        Log::info('Verificando progreso de documentos:', [
            'tramite_id' => $tramite->id,
            'tipo_persona' => $tipoPersona,
            'documentos_requeridos' => $totalDocumentosRequeridos,
            'documentos_subidos' => $documentosSubidos
        ]);

        // Si se han subido todos los documentos requeridos, actualizar progreso
        if ($documentosSubidos >= $totalDocumentosRequeridos) {
            // Actualizar progreso según el tipo de persona
            $progresoFinal = $tipoPersona === 'Física' ? 3 : 6;
            $tramite->actualizarProgresoSeccion($progresoFinal);
            
            Log::info('✅ Progreso actualizado - Documentos completados', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
                'progreso_final' => $progresoFinal
            ]);
        }
    }

    /**
     * Finaliza el trámite y cambia su estado a "En Revision"
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizarTramite(Request $request)
    {
        try {
            $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id'
            ]);

            $tramite = Tramite::with('solicitante')->find($request->tramite_id);
            
            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            $tipoPersona = $tramite->solicitante->tipo_persona;

            // Verificar que todos los documentos del tipo de persona estén subidos
            $totalDocumentosRequeridos = Documento::where(function($query) use ($tipoPersona) {
                $query->where('tipo_persona', $tipoPersona)
                      ->orWhere('tipo_persona', 'Ambas');
            })
            ->where('es_visible', true)
            ->count();

            $documentosSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                ->whereHas('documento', function($query) use ($tipoPersona) {
                    $query->where('es_visible', true)
                          ->where(function($subQuery) use ($tipoPersona) {
                              $subQuery->where('tipo_persona', $tipoPersona)
                                       ->orWhere('tipo_persona', 'Ambas');
                          });
                })
                ->count();

            Log::info('Verificando documentos para finalizar trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
                'documentos_requeridos' => $totalDocumentosRequeridos,
                'documentos_subidos' => $documentosSubidos
            ]);

            if ($documentosSubidos < $totalDocumentosRequeridos) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe subir todos los documentos requeridos antes de finalizar el trámite',
                    'debug' => [
                        'tipo_persona' => $tipoPersona,
                        'requeridos' => $totalDocumentosRequeridos,
                        'subidos' => $documentosSubidos
                    ]
                ], 400);
            }

            // Actualizar el trámite según el tipo de persona
            $progresoFinal = $tipoPersona === 'Física' ? 3 : 6;
            $tramite->update([
                'estado' => 'En Revision',
                'progreso_tramite' => $progresoFinal,
                'fecha_finalizacion' => now()
            ]);

            Log::info('✅ Trámite finalizado exitosamente', [
                'tramite_id' => $tramite->id,
                'estado' => $tramite->estado,
                'progreso' => $tramite->progreso_tramite,
                'tipo_persona' => $tipoPersona
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite enviado correctamente para revisión',
                'tramite_id' => $tramite->id,
                'estado' => $tramite->estado,
                'redirect_url' => route('tramites.solicitante.estado', $tramite->id)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error al finalizar trámite:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descarga o visualiza un documento
     *
     * @param Request $request
     * @param int $tramiteId
     * @param int $documentoId
     * @return \Illuminate\Http\Response
     */
    public function verDocumento(Request $request, $tramiteId, $documentoId)
    {
        try {
            // Verificar que el documento pertenece al usuario actual
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            $documentoSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->first();

            if (!$documentoSolicitante || !$documentoSolicitante->ruta_archivo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            // Desencriptar la ruta del archivo
            try {
                $rutaArchivo = Crypt::decryptString($documentoSolicitante->ruta_archivo);
            } catch (\Exception $e) {
                // Si no está encriptado, usar la ruta directamente
                $rutaArchivo = $documentoSolicitante->ruta_archivo;
            }

            $rutaCompleta = storage_path('app/public/' . $rutaArchivo);

            if (!file_exists($rutaCompleta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no existe en el servidor'
                ], 404);
            }

            $documento = Documento::find($documentoId);
            $nombreArchivo = $documento->nombre . '.pdf';

            // Detectar si es móvil para forzar descarga
            $userAgent = $request->header('User-Agent');
            $esMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);

            if ($esMobile || $request->get('download') === '1') {
                // Forzar descarga en móviles
                return response()->download($rutaCompleta, $nombreArchivo);
            } else {
                // Mostrar en el navegador (desktop)
                return response()->file($rutaCompleta, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al ver documento:', [
                'tramite_id' => $tramiteId,
                'documento_id' => $documentoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al acceder al documento'
            ], 500);
        }
    }
} 