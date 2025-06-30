<?php

namespace App\Http\Controllers;

use App\Models\DocumentoSolicitante;
use App\Models\Documento;
use App\Models\Tramite;
use App\Models\DocumentoVersion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentoSolicitanteController extends Controller
{
    /**
     * Process and store a document for a solicitante
     *
     * @param UploadedFile $file
     * @param Tramite $tramite
     * @param string $documentoNombre
     * @return DocumentoSolicitante
     */
    public function processDocument(UploadedFile $file, Tramite $tramite, string $documentoNombre = 'Constancia de Situación Fiscal'): DocumentoSolicitante
    {
        try {
            // Buscar o crear el documento
            $documento = $this->findOrCreateDocumento($documentoNombre);

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('doc_' . $documento->id . '_') . '.' . $extension;
            
            // Almacenar archivo
            $ruta = $file->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');
            $rutaEncriptada = Crypt::encryptString($ruta);

            // Crear registro en documento_solicitante
            $documentoSolicitante = DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Aprobado',
                'version_documento' => 1,
                'ruta_archivo' => $rutaEncriptada,
            ]);

            Log::info('Documento procesado y almacenado exitosamente', [
                'documento_solicitante_id' => $documentoSolicitante->id,
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'archivo' => $nombreArchivo
            ]);

            return $documentoSolicitante;

        } catch (\Exception $e) {
            Log::error('Error al procesar documento', [
                'error' => $e->getMessage(),
                'tramite_id' => $tramite->id,
                'documento_nombre' => $documentoNombre
            ]);
            throw new \Exception('Error al procesar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Find or create a documento record
     *
     * @param string $nombre
     * @return Documento
     */
    private function findOrCreateDocumento(string $nombre): Documento
    {
        $documento = Documento::where('nombre', 'LIKE', '%' . $nombre . '%')->first();

        if (!$documento) {
            $documento = Documento::create([
                'nombre' => $nombre,
                'tipo_persona' => 'Ambas',
                'descripcion' => $nombre . ' con código QR del SAT',
                'es_visible' => true,
            ]);

            Log::info('Nuevo documento creado', [
                'documento_id' => $documento->id,
                'nombre' => $nombre
            ]);
        }

        return $documento;
    }

    /**
     * Update document status
     *
     * @param DocumentoSolicitante $documentoSolicitante
     * @param string $estado
     * @param string|null $observaciones
     * @return DocumentoSolicitante
     */
    public function updateStatus(DocumentoSolicitante $documentoSolicitante, string $estado, ?string $observaciones = null): DocumentoSolicitante
    {
        try {
            $documentoSolicitante->update([
                'estado' => $estado,
                'observaciones' => $observaciones,
            ]);

            Log::info('Estado del documento actualizado', [
                'documento_solicitante_id' => $documentoSolicitante->id,
                'nuevo_estado' => $estado
            ]);

            return $documentoSolicitante;

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado del documento', [
                'error' => $e->getMessage(),
                'documento_solicitante_id' => $documentoSolicitante->id
            ]);
            throw new \Exception('Error al actualizar el estado del documento: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el documento solicitado.
     *
     * @param DocumentoSolicitante $documentoSolicitante
     * @return StreamedResponse
     */
    public function ver(DocumentoSolicitante $documentoSolicitante)
    {
        // Verificar que el usuario tenga acceso al documento
        // $this->authorize('ver', $documentoSolicitante);

        if (!$documentoSolicitante->ruta_archivo) {
            abort(404, 'Archivo no encontrado');
        }

        try {
            // Desencriptar la ruta del archivo
            $rutaDesencriptada = Crypt::decryptString($documentoSolicitante->ruta_archivo);
            
            if (!Storage::disk('public')->exists($rutaDesencriptada)) {
                abort(404, 'Archivo no encontrado en el storage');
            }

            $filename = basename($rutaDesencriptada);
            $fullPath = Storage::disk('public')->path($rutaDesencriptada);
            $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

            return response()->stream(function () use ($rutaDesencriptada) {
                echo Storage::disk('public')->get($rutaDesencriptada);
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al desencriptar ruta de archivo:', [
                'documento_solicitante_id' => $documentoSolicitante->id,
                'error' => $e->getMessage()
            ]);
            abort(404, 'No se pudo acceder al archivo');
        }
    }

    /**
     * Muestra una versión específica del documento.
     *
     * @param DocumentoVersion $documentoVersion
     * @return StreamedResponse
     */
    public function verVersion(DocumentoVersion $documentoVersion)
    {
        // Verificar que el usuario tenga acceso a la versión del documento
        $this->authorize('ver', $documentoVersion->documentoSolicitante);

        if (!Storage::exists($documentoVersion->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::response($documentoVersion->archivo);
    }
} 