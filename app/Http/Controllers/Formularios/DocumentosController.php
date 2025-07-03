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
        try {
            Log::info('=== INICIO subir documento ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['archivo']) // No logear el archivo completo
            ]);

            // Validar la solicitud con validaciones robustas
            $validated = $this->validateSubirDocumento($request);

            $solicitante = Auth::user()->solicitante;
            
            Log::info('Intentando subir documento', [
                'user_id' => Auth::id(),
                'solicitante_id' => $solicitante->id,
                'documento_id' => $validated['documento_id']
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

            return DB::transaction(function () use ($validated, $tramite) {
                $documento = Documento::find($validated['documento_id']);
                $ruta = $this->storeArchivo($validated['archivo'], $tramite->id, $documento->id);
                $docSolicitante = $this->guardarDocumentoSolicitante($tramite->id, $documento->id, $ruta);

                // Verificar si se han subido todos los documentos requeridos
                $this->verificarYActualizarProgreso($tramite);

                Log::info('✅ Documento subido exitosamente:', [
                    'tramite_id' => $tramite->id,
                    'documento_id' => $documento->id,
                    'documento_solicitante_id' => $docSolicitante->id
                ]);

                return response()->json([
                    'success' => true,
                    'ruta' => asset('storage/' . $ruta),
                    'docSolicitanteId' => $docSolicitante->id,
                    'mensaje' => 'Documento subido correctamente',
                ]);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación en subida de documento:', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['archivo'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en el documento.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'seccion' => 'documentos',
                    'timestamp' => now()->toISOString(),
                    'total_errores' => count($e->errors())
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al subir documento:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['archivo'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al subir el documento. Por favor, intente nuevamente.',
                'debug_info' => [
                    'seccion' => 'documentos',
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida los datos para subir un documento con validaciones robustas
     *
     * @param Request $request La solicitud a validar
     * @return array Los datos validados
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateSubirDocumento(Request $request)
    {
        $rules = [
            'documento_id' => [
                'required',
                'integer',
                'exists:documento,id'
            ],
            'archivo' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240', // 10MB
                'min:1'
            ]
        ];

        $messages = [
            'documento_id.required' => 'Debe especificar el tipo de documento a subir',
            'documento_id.integer' => 'El tipo de documento debe ser un número válido',
            'documento_id.exists' => 'El tipo de documento especificado no es válido',
            
            'archivo.required' => 'Debe seleccionar un archivo para subir',
            'archivo.file' => 'El archivo seleccionado no es válido',
            'archivo.mimes' => 'Solo se permiten archivos en formato PDF',
            'archivo.max' => 'El archivo no puede ser mayor a 10 MB',
            'archivo.min' => 'El archivo está vacío o dañado'
        ];

        $validated = $request->validate($rules, $messages);

        // Validaciones adicionales del archivo
        $this->validateArchivoAdicional($validated['archivo']);

        return $validated;
    }

    /**
     * Validaciones adicionales del archivo subido
     *
     * @param \Illuminate\Http\UploadedFile $archivo
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateArchivoAdicional($archivo)
    {
        // Verificar que el archivo es realmente un PDF
        $mimeType = $archivo->getMimeType();
        $allowedMimes = ['application/pdf'];
        
        if (!in_array($mimeType, $allowedMimes)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El archivo debe ser un PDF válido'
            ]);
        }

        // Verificar extensión del archivo
        $extension = strtolower($archivo->getClientOriginalExtension());
        if ($extension !== 'pdf') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El archivo debe tener extensión .pdf'
            ]);
        }

        // Verificar que el archivo no esté corrupto
        if (!$archivo->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El archivo está dañado o corrupto. Por favor, seleccione otro archivo.'
            ]);
        }

        // Verificar tamaño mínimo (al menos 1 KB)
        if ($archivo->getSize() < 1024) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El archivo es demasiado pequeño. Debe tener al menos 1 KB.'
            ]);
        }

        // Verificar nombre del archivo
        $originalName = $archivo->getClientOriginalName();
        if (strlen($originalName) > 255) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El nombre del archivo es demasiado largo. Máximo 255 caracteres.'
            ]);
        }

        // Verificar que el nombre del archivo no contenga caracteres peligrosos
        if (!preg_match('/^[a-zA-Z0-9\s\.\-_\(\)]+\.pdf$/i', $originalName)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'archivo' => 'El nombre del archivo contiene caracteres no permitidos. Use solo letras, números, espacios, guiones y paréntesis.'
            ]);
        }
    }

    /**
     * Obtiene los documentos asociados a un trámite con validaciones
     *
     * @param Request $request La solicitud HTTP
     * @param int $tramiteId El ID del trámite
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los documentos
     */
    public function get(Request $request, $tramiteId)
    {
        try {
            Log::info('=== INICIO obtener documentos ===', [
                'user_id' => Auth::id(),
                'tramite_id' => $tramiteId
            ]);

            // Validar la solicitud
            $this->validateObtenerDocumentos($request, $tramiteId);

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

            Log::info('✅ Documentos obtenidos exitosamente:', [
                'tramite_id' => $tramiteId,
                'total_documentos' => count($documentos)
            ]);

            return response()->json([
                'success' => true,
                'documentos' => $documentos,
                'mensaje' => 'Documentos obtenidos correctamente.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación al obtener documentos:', [
                'errors' => $e->errors(),
                'tramite_id' => $tramiteId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error en los parámetros de la solicitud.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al obtener documentos:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tramite_id' => $tramiteId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al obtener los documentos.',
                'debug_info' => [
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida la solicitud para obtener documentos
     *
     * @param Request $request
     * @param int $tramiteId
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateObtenerDocumentos(Request $request, $tramiteId)
    {
        $rules = [
            'tramiteId' => [
                'required',
                'integer',
                'exists:tramite,id'
            ]
        ];

        $messages = [
            'tramiteId.required' => 'Debe especificar el ID del trámite',
            'tramiteId.integer' => 'El ID del trámite debe ser un número válido',
            'tramiteId.exists' => 'El trámite especificado no existe'
        ];

        // Agregar el tramiteId al request para validación
        $request->merge(['tramiteId' => $tramiteId]);
        $request->validate($rules, $messages);
    }

    /**
     * Valida los datos de la solicitud con validaciones robustas
     *
     * @param Request $request La solicitud a validar
     * @param string $method El método que se está ejecutando (subir o get)
     * @param int|null $tramiteId El ID del trámite, si aplica
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request, string $method, $tramiteId = null)
    {
        if ($method === 'subir') {
            // Para subir, usar la validación robusta
            $this->validateSubirDocumento($request);
        } elseif ($method === 'get') {
            // Para obtener, validar el tramiteId
            $this->validateObtenerDocumentos($request, $tramiteId);
        } elseif ($method === 'update') {
            $this->validateActualizarEstado($request, $tramiteId);
        }
    }

    /**
     * Valida la actualización del estado de un documento
     *
     * @param Request $request
     * @param int $tramiteId
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateActualizarEstado(Request $request, $tramiteId)
    {
        $rules = [
            'tramiteId' => [
                'required',
                'integer',
                'exists:tramite,id'
            ],
            'documento_id' => [
                'required',
                'integer',
                'exists:documento,id'
            ],
            'approval' => [
                'required',
                'in:approved,not-approved'
            ],
            'comment' => [
                'nullable',
                'string',
                'max:1000',
                'min:3'
            ]
        ];

        $messages = [
            'tramiteId.required' => 'Debe especificar el ID del trámite',
            'tramiteId.integer' => 'El ID del trámite debe ser un número válido',
            'tramiteId.exists' => 'El trámite especificado no existe',
            
            'documento_id.required' => 'Debe especificar el ID del documento',
            'documento_id.integer' => 'El ID del documento debe ser un número válido',
            'documento_id.exists' => 'El documento especificado no existe',
            
            'approval.required' => 'Debe especificar si el documento se aprueba o rechaza',
            'approval.in' => 'El estado de aprobación debe ser "approved" o "not-approved"',
            
            'comment.string' => 'El comentario debe ser texto válido',
            'comment.max' => 'El comentario no puede exceder 1000 caracteres',
            'comment.min' => 'El comentario debe tener al menos 3 caracteres'
        ];

        $request->merge(['tramiteId' => $tramiteId]);
        $request->validate($rules, $messages);
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
        try {
            Log::info('=== INICIO actualizar estado documento ===', [
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar la solicitud
            $this->validateActualizarEstado($request, $tramiteId);

            $docSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->firstOrFail();
            
            $estadoAnterior = $docSolicitante->estado;
            $nuevoEstado = $request->input('approval') === 'approved' ? 'Aprobado' : 'Rechazado';
            
            $docSolicitante->estado = $nuevoEstado;
            $docSolicitante->observaciones = $request->input('comment');
            $docSolicitante->save();

            Log::info('✅ Estado de documento actualizado exitosamente:', [
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => $nuevoEstado,
                'observaciones' => $request->input('comment')
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento actualizado correctamente.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación al actualizar documento:', [
                'errors' => $e->errors(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en la solicitud.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('❌ Documento no encontrado:', [
                'tramiteId' => $tramiteId, 
                'documentoId' => $documentoId
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Documento no encontrado.',
            ], 404);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('❌ Error de base de datos al actualizar documento:', [
                'message' => $e->getMessage(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error de base de datos al actualizar el documento.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ Error inesperado al actualizar documento:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error inesperado al actualizar el documento.',
                'debug_info' => [
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
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
            Log::info('=== INICIO finalizar trámite ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar la solicitud
            $validated = $this->validateFinalizarTramite($request);

            $tramite = Tramite::with('solicitante')->find($validated['tramite_id']);
            
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación al finalizar trámite:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en la solicitud.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al finalizar trámite:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el trámite.',
                'debug_info' => [
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida la solicitud para finalizar el trámite
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFinalizarTramite(Request $request)
    {
        $rules = [
            'tramite_id' => [
                'required',
                'integer',
                'exists:tramite,id'
            ]
        ];

        $messages = [
            'tramite_id.required' => 'Debe especificar el ID del trámite',
            'tramite_id.integer' => 'El ID del trámite debe ser un número válido',
            'tramite_id.exists' => 'El trámite especificado no existe'
        ];

        return $request->validate($rules, $messages);
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
                Log::warning('Solicitante no encontrado', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                Log::warning('Trámite no encontrado', [
                    'tramite_id' => $tramiteId,
                    'solicitante_id' => $solicitante->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            $documentoSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->first();

            if (!$documentoSolicitante || !$documentoSolicitante->ruta_archivo) {
                Log::warning('Documento solicitante no encontrado', [
                    'tramite_id' => $tramiteId,
                    'documento_id' => $documentoId,
                    'existe_registro' => $documentoSolicitante ? 'SI' : 'NO',
                    'tiene_ruta' => $documentoSolicitante && $documentoSolicitante->ruta_archivo ? 'SI' : 'NO'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            Log::info('Intentando acceder a documento', [
                'tramite_id' => $tramiteId,
                'documento_id' => $documentoId,
                'ruta_encriptada' => $documentoSolicitante->ruta_archivo
            ]);

            // Desencriptar la ruta del archivo
            $rutaArchivo = '';
            try {
                // Intentar desencriptar con el nuevo método (encrypt/decrypt)
                $rutaArchivo = decrypt($documentoSolicitante->ruta_archivo);
                Log::info('Ruta desencriptada con decrypt()', ['ruta' => $rutaArchivo]);
            } catch (\Exception $e1) {
                try {
                    // Intentar desencriptar con Crypt
                    $rutaArchivo = Crypt::decryptString($documentoSolicitante->ruta_archivo);
                    Log::info('Ruta desencriptada con Crypt::decryptString()', ['ruta' => $rutaArchivo]);
                } catch (\Exception $e2) {
                    // Si no se puede desencriptar, usar la ruta directamente
                    $rutaArchivo = $documentoSolicitante->ruta_archivo;
                    Log::info('Usando ruta sin encriptar', ['ruta' => $rutaArchivo]);
                }
            }

            // Probar diferentes ubicaciones posibles del archivo
            $rutasPosibles = [
                storage_path('app/public/' . $rutaArchivo),
                storage_path('app/public/documentos_solicitante/' . $tramiteId . '/' . basename($rutaArchivo)),
                storage_path('app/public/documentos_tramite/' . $tramiteId . '/' . basename($rutaArchivo)),
                storage_path('app/' . $rutaArchivo)
            ];

            $rutaCompleta = null;
            foreach ($rutasPosibles as $ruta) {
                Log::info('Verificando ruta', ['ruta' => $ruta, 'existe' => file_exists($ruta)]);
                if (file_exists($ruta)) {
                    $rutaCompleta = $ruta;
                    break;
                }
            }

            if (!$rutaCompleta) {
                Log::error('Archivo no encontrado en ninguna ubicación', [
                    'tramite_id' => $tramiteId,
                    'documento_id' => $documentoId,
                    'ruta_original' => $rutaArchivo,
                    'rutas_probadas' => $rutasPosibles
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no existe en el servidor'
                ], 404);
            }

            Log::info('Archivo encontrado', ['ruta_final' => $rutaCompleta]);

            $documento = Documento::find($documentoId);
            $nombreArchivo = ($documento ? $documento->nombre : 'Documento') . '.pdf';

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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al acceder al documento: ' . $e->getMessage()
            ], 500);
        }
    }
} 