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

class LocalDocumentoController extends Controller
{
    /**
     * Sube un documento usando almacenamiento local simple
     */
    public function subirDocumento(Request $request)
    {
        try {
            // Validación simple con límite de 50MB (consistente con servidor)
            $request->validate([
                'archivo' => 'required|file|mimes:pdf|max:51200', // 50MB en KB
                'documento_id' => 'required|integer|exists:documento,id'
            ]);

            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Obtener trámite activo
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró un trámite en progreso'
                ], 404);
            }

            $file = $request->file('archivo');
            $documentoId = $request->documento_id;

            // Guardar archivo localmente en storage/app/public
            $fileName = time() . '_' . $documentoId . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'documentos_solicitante/' . $tramite->id, 
                $fileName, 
                'public'
            );

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
                    'ruta_archivo' => $filePath, // Guardar ruta sin encriptar
                    'nombre_original' => $file->getClientOriginalName()
                ]
            );

            // Log simple
            Log::info('Documento subido localmente', [
                'tramite_id' => $tramite->id,
                'documento_id' => $documentoId,
                'archivo' => $fileName,
                'tamaño' => $file->getSize()
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento subido correctamente',
                'ruta' => $filePath,
                'docSolicitanteId' => $documentoSolicitante->id,
                'url' => asset('storage/' . $filePath)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error de validación: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
            
        } catch (\Exception $e) {
            // Manejo específico para errores de tamaño
            if (strpos($e->getMessage(), 'size') !== false || strpos($e->getMessage(), 'large') !== false) {
                Log::error('Error de tamaño al subir documento', [
                    'error' => $e->getMessage(),
                    'archivo' => $request->file('archivo') ? $request->file('archivo')->getClientOriginalName() : 'N/A',
                    'tamaño' => $request->file('archivo') ? $request->file('archivo')->getSize() : 'N/A'
                ]);
                
                return response()->json([
                    'success' => false,
                    'mensaje' => 'El archivo es demasiado grande. El tamaño máximo permitido es 50MB.'
                ], 413);
            }
            
            Log::error('Error al subir documento local', [
                'error' => $e->getMessage(),
                'archivo' => $request->file('archivo') ? $request->file('archivo')->getClientOriginalName() : 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
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
                return response()->json(['error' => 'Archivo no existe'], 404);
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
} 