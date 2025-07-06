<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LocalDocumentoController extends Controller
{
    /**
     * Sube un documento usando almacenamiento local optimizado
     */
    public function subirDocumento(Request $request)
    {
        // Configurar límites dinámicamente
        $this->configurarLimitesUpload();
        
        // Log inicial detallado
        $this->logInicial($request);
        
        try {
            // Validaciones previas básicas
            $validacionPrevia = $this->validacionesBasicas($request);
            if ($validacionPrevia !== null) {
                return $validacionPrevia;
            }

            // Obtener datos del usuario y trámite
            $datosUsuario = $this->obtenerDatosUsuario();
            if (!$datosUsuario['success']) {
                return response()->json($datosUsuario, 404);
            }

            $tramite = $datosUsuario['tramite'];
            $file = $request->file('archivo');
            $documentoId = $request->documento_id;

            // Validar archivo manualmente (evitar validación de Laravel)
            $validacionArchivo = $this->validarArchivoManual($file);
            if (!$validacionArchivo['success']) {
                return response()->json($validacionArchivo, 422);
            }

            // Procesar y guardar archivo
            $resultado = $this->procesarArchivo($file, $tramite, $documentoId);
            
            if (!$resultado['success']) {
                return response()->json($resultado, 500);
            }

            Log::info('✅ Documento subido exitosamente', [
                'tramite_id' => $tramite->id,
                'documento_id' => $documentoId,
                'archivo' => $resultado['fileName'],
                'tamaño_bytes' => $file->getSize(),
                'tamaño_mb' => round($file->getSize() / 1024 / 1024, 2)
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento subido correctamente',
                'ruta' => $resultado['filePath'],
                'docSolicitanteId' => $resultado['docSolicitanteId'],
                'url' => asset('storage/' . $resultado['filePath']),
                'info' => [
                    'tamaño_mb' => round($file->getSize() / 1024 / 1024, 2),
                    'nombre_original' => $file->getClientOriginalName()
                ]
            ]);

        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            return $this->manejarErrorTamano($e, $request);
            
        } catch (\Exception $e) {
            return $this->manejarErrorGeneral($e, $request);
        }
    }

    /**
     * Configura límites de upload dinámicamente
     */
    private function configurarLimitesUpload()
    {
        // Intentar configurar límites más altos
        try {
            ini_set('upload_max_filesize', '100M');
            ini_set('post_max_size', '110M');
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);
            ini_set('max_input_time', 300);
            
            Log::info('⚙️ Límites configurados dinámicamente', [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit')
            ]);
        } catch (\Exception $e) {
            Log::warning('⚠️ No se pudieron configurar límites dinámicamente', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log inicial detallado
     */
    private function logInicial(Request $request)
    {
        $contentLength = $request->header('Content-Length');
        $hasFile = $request->hasFile('archivo');
        
        Log::info('📤 INICIO SUBIDA DE DOCUMENTO', [
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => $contentLength,
            'content_length_mb' => $contentLength ? round($contentLength / 1024 / 1024, 2) : null,
            'has_file' => $hasFile,
            'documento_id' => $request->input('documento_id'),
            'php_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ]
        ]);
    }

    /**
     * Validaciones básicas sin usar las reglas de Laravel
     */
    private function validacionesBasicas(Request $request)
    {
        // Verificar Content-Length
        $contentLength = $request->header('Content-Length');
        if ($contentLength && $contentLength > (100 * 1024 * 1024)) {
            Log::warning('❌ Archivo excede 100MB', [
                'content_length' => $contentLength,
                'content_length_mb' => round($contentLength / 1024 / 1024, 2)
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'El archivo excede el límite de 100MB permitido'
            ], 413);
        }

        // Verificar que llegó el archivo
        if (!$request->hasFile('archivo')) {
            Log::warning('❌ No se recibió archivo en la petición');
            return response()->json([
                'success' => false,
                'mensaje' => 'No se recibió ningún archivo'
            ], 422);
        }

        // Verificar documento_id
        if (!$request->has('documento_id') || !is_numeric($request->documento_id)) {
            Log::warning('❌ ID de documento inválido', [
                'documento_id' => $request->input('documento_id')
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'ID de documento inválido'
            ], 422);
        }

        return null; // Sin errores
    }

    /**
     * Obtener datos del usuario y trámite
     */
    private function obtenerDatosUsuario()
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            Log::error('❌ Solicitante no encontrado', ['user_id' => $user->id]);
            return [
                'success' => false,
                'mensaje' => 'No se encontró información del solicitante'
            ];
        }

        // Obtener trámite activo
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->whereIn('estado', ['Pendiente', 'En Revision'])
            ->latest()
            ->first();

        if (!$tramite) {
            Log::error('❌ Trámite no encontrado', ['solicitante_id' => $solicitante->id]);
            return [
                'success' => false,
                'mensaje' => 'No se encontró un trámite en progreso'
            ];
        }

        Log::info('✅ Datos de usuario obtenidos', [
            'user_id' => $user->id,
            'solicitante_id' => $solicitante->id,
            'tramite_id' => $tramite->id,
            'estado_tramite' => $tramite->estado
        ]);

        return [
            'success' => true,
            'user' => $user,
            'solicitante' => $solicitante,
            'tramite' => $tramite
        ];
    }

    /**
     * Validar archivo manualmente sin usar las reglas de Laravel
     */
    private function validarArchivoManual($file)
    {
        if (!$file || !$file->isValid()) {
            Log::error('❌ Archivo inválido o corrupto');
            return [
                'success' => false,
                'mensaje' => 'El archivo está corrupto o es inválido'
            ];
        }

        $fileSize = $file->getSize();
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        Log::info('📋 Detalles del archivo', [
            'nombre_original' => $file->getClientOriginalName(),
            'tamaño_bytes' => $fileSize,
            'tamaño_mb' => $fileSizeMB,
            'mime_type' => $mimeType,
            'extension' => $extension
        ]);

        // Validar tamaño (100MB máximo)
        if ($fileSize > (100 * 1024 * 1024)) {
            Log::warning('❌ Archivo excede 100MB', [
                'tamaño_mb' => $fileSizeMB
            ]);
            return [
                'success' => false,
                'mensaje' => "El archivo es demasiado grande ({$fileSizeMB}MB). Máximo permitido: 100MB"
            ];
        }

        // Validar tipo de archivo de forma más flexible
        $esPDF = false;
        
        // Verificar por extensión
        if ($extension === 'pdf') {
            $esPDF = true;
        }
        
        // Verificar por MIME type
        if (in_array($mimeType, ['application/pdf', 'application/x-pdf'])) {
            $esPDF = true;
        }

        if (!$esPDF) {
            Log::warning('❌ Tipo de archivo no permitido', [
                'mime_type' => $mimeType,
                'extension' => $extension
            ]);
            return [
                'success' => false,
                'mensaje' => 'Solo se permiten archivos PDF'
            ];
        }

        Log::info('✅ Archivo válido para procesamiento');
        return ['success' => true];
    }

    /**
     * Procesar y guardar archivo
     */
    private function procesarArchivo($file, $tramite, $documentoId)
    {
        try {
            // Verificar que el documento existe
            $documento = Documento::find($documentoId);
            if (!$documento) {
                return [
                    'success' => false,
                    'mensaje' => 'Tipo de documento no válido'
                ];
            }

            // Generar nombre único para el archivo
            $fileName = time() . '_' . $documentoId . '_' . uniqid() . '.pdf';
            $relativePath = 'documentos_solicitante/' . $tramite->id;
            
            Log::info('💾 Iniciando almacenamiento', [
                'archivo_destino' => $fileName,
                'ruta_relativa' => $relativePath
            ]);

            // Guardar archivo
            $filePath = $file->storeAs($relativePath, $fileName, 'public');
            
            if (!$filePath) {
                throw new \Exception('No se pudo almacenar el archivo');
            }

            // Verificar que el archivo se guardó correctamente
            $fullPath = storage_path('app/public/' . $filePath);
            if (!file_exists($fullPath)) {
                throw new \Exception('El archivo no se encontró después de guardarlo');
            }

            $savedFileSize = filesize($fullPath);
            Log::info('✅ Archivo almacenado físicamente', [
                'ruta_completa' => $fullPath,
                'tamaño_guardado' => $savedFileSize,
                'tamaño_original' => $file->getSize()
            ]);

            // Crear o actualizar registro en base de datos
            $documentoSolicitante = DocumentoSolicitante::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'documento_id' => $documentoId
                ],
                [
                    'fecha_entrega' => now(),
                    'estado' => 'Pendiente',
                    'version_documento' => 1,
                    'ruta_archivo' => $filePath,
                    'nombre_original' => $file->getClientOriginalName()
                ]
            );

            Log::info('✅ Registro en BD creado/actualizado', [
                'documento_solicitante_id' => $documentoSolicitante->id
            ]);

            return [
                'success' => true,
                'filePath' => $filePath,
                'fileName' => $fileName,
                'docSolicitanteId' => $documentoSolicitante->id
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al procesar archivo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'mensaje' => 'Error al procesar el archivo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Manejar error de tamaño
     */
    private function manejarErrorTamano($e, $request)
    {
        Log::error('❌ ERROR DE TAMAÑO (PostTooLargeException)', [
            'content_length' => $request->header('Content-Length'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize')
        ]);
        
        return response()->json([
            'success' => false,
            'mensaje' => 'El archivo es demasiado grande. Límites actuales: upload=' . ini_get('upload_max_filesize') . ', post=' . ini_get('post_max_size')
        ], 413);
    }

    /**
     * Manejar error general
     */
    private function manejarErrorGeneral($e, $request)
    {
        Log::error('❌ ERROR GENERAL EN SUBIDA', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'archivo' => $request->file('archivo') ? $request->file('archivo')->getClientOriginalName() : 'N/A',
            'content_length' => $request->header('Content-Length')
        ]);
        
        // Verificar si es un error relacionado con tamaño
        $mensaje = $e->getMessage();
        if (str_contains($mensaje, 'size') || str_contains($mensaje, 'large') || str_contains($mensaje, 'upload')) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El archivo es demasiado grande o hay un problema con la configuración del servidor'
            ], 413);
        }
        
        return response()->json([
            'success' => false,
            'mensaje' => 'Error interno del servidor: ' . $mensaje
        ], 500);
    }

    /**
     * Ver documento almacenado localmente
     */
    public function verDocumento($tramiteId, $documentoId)
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // Verificar que el trámite pertenece al usuario
            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                return response()->json(['error' => 'Trámite no encontrado'], 404);
            }

            // Buscar el documento
            $documentoSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->first();

            if (!$documentoSolicitante || !$documentoSolicitante->ruta_archivo) {
                return response()->json(['error' => 'Documento no encontrado'], 404);
            }

            // Construir ruta del archivo
            $filePath = storage_path('app/public/' . $documentoSolicitante->ruta_archivo);

            if (!file_exists($filePath)) {
                Log::error('Archivo físico no encontrado', [
                    'ruta_bd' => $documentoSolicitante->ruta_archivo,
                    'ruta_completa' => $filePath
                ]);
                return response()->json(['error' => 'Archivo no existe físicamente'], 404);
            }

            // Obtener información del documento para el nombre
            $documento = Documento::find($documentoId);
            $fileName = ($documento ? $documento->nombre : 'Documento') . '.pdf';

            // Retornar el archivo
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al ver documento local', [
                'tramite_id' => $tramiteId,
                'documento_id' => $documentoId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Error al acceder al documento'], 500);
        }
    }

    /**
     * Obtener lista de documentos del trámite
     */
    public function obtenerDocumentos()
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tipoPersona = $solicitante->tipo_persona;

            // Obtener trámite activo
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            // Obtener documentos requeridos
            $documentos = Documento::where(function($query) use ($tipoPersona) {
                $query->where('tipo_persona', $tipoPersona)
                      ->orWhere('tipo_persona', 'Ambas');
            })
            ->where('es_visible', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'descripcion', 'tipo_persona']);

            // Si hay trámite, verificar documentos subidos
            if ($tramite) {
                $documentosSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                    ->get()
                    ->keyBy('documento_id');

                $documentos = $documentos->map(function($documento) use ($documentosSubidos) {
                    $docSubido = $documentosSubidos->get($documento->id);
                    
                    return [
                        'id' => $documento->id,
                        'nombre' => $documento->nombre,
                        'descripcion' => $documento->descripcion,
                        'tipo_persona' => $documento->tipo_persona,
                        'estado' => $docSubido ? ucfirst($docSubido->estado) : 'Pendiente',
                        'fecha_entrega' => $docSubido ? $docSubido->fecha_entrega : null,
                        'ruta_archivo' => $docSubido ? true : null,
                        'observaciones' => $docSubido ? $docSubido->observaciones : null,
                        'url' => $docSubido ? asset('storage/' . $docSubido->ruta_archivo) : null
                    ];
                });
            } else {
                $documentos = $documentos->map(function($documento) {
                    return [
                        'id' => $documento->id,
                        'nombre' => $documento->nombre,
                        'descripcion' => $documento->descripcion,
                        'tipo_persona' => $documento->tipo_persona,
                        'estado' => 'Pendiente',
                        'fecha_entrega' => null,
                        'ruta_archivo' => null,
                        'url' => null
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'documentos' => $documentos,
                'tipo_persona' => $tipoPersona,
                'tramite_id' => $tramite ? $tramite->id : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener documentos locales', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Loggear errores de upload enviados desde el frontend
     */
    public function logUploadError(Request $request)
    {
        try {
            $errorData = $request->all();
            
            // Log el error con información detallada
            Log::error('🚨 ERROR DE UPLOAD REPORTADO POR FRONTEND', [
                'timestamp' => now()->toISOString(),
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'frontend_error_data' => $errorData,
                'session_id' => session()->getId(),
                'php_limits_servidor' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'max_input_time' => ini_get('max_input_time'),
                    'max_file_uploads' => ini_get('max_file_uploads'),
                ],
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
                    'memory_usage' => memory_get_usage(true),
                    'memory_peak' => memory_get_peak_usage(true),
                ]
            ]);
            
            // También log en un archivo separado para errores de upload
            Log::channel('single')->error('FRONTEND_UPLOAD_ERROR', [
                'error_type' => $errorData['error_type'] ?? 'unknown',
                'status_code' => $errorData['status_code'] ?? 'unknown',
                'file_size_mb' => $errorData['file_info']['size_mb'] ?? 'unknown',
                'timestamp' => $errorData['timestamp'] ?? now()->toISOString(),
                'user_id' => Auth::id(),
                'tramite_id' => $errorData['tramite_id'] ?? 'unknown',
                'server_response' => $errorData['server_response'] ?? [],
                'browser_info' => [
                    'user_agent' => $errorData['user_agent'] ?? $request->userAgent(),
                    'url' => $errorData['url'] ?? $request->url(),
                    'platform' => $errorData['browser_limits']['platform'] ?? 'unknown',
                    'connection' => $errorData['browser_limits']['connection_type'] ?? 'unknown',
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Error registrado correctamente en logs del servidor'
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error al procesar log de error de frontend', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el log'
            ], 500);
        }
    }
} 