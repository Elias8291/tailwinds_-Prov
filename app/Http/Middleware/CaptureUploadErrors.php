<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CaptureUploadErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            
            // Si la respuesta tiene error 413, loggearlo
            if ($response->getStatusCode() === 413) {
                $this->logUploadError($request, '413 Content Too Large', [
                    'content_length' => $request->header('Content-Length'),
                    'php_limits' => $this->getPHPLimits(),
                    'request_data' => [
                        'url' => $request->url(),
                        'method' => $request->method(),
                        'has_files' => $request->hasFile('archivo'),
                        'user_agent' => $request->userAgent(),
                    ]
                ]);
            }
            
            return $response;
            
        } catch (PostTooLargeException $e) {
            // Capturar específicamente PostTooLargeException
            $this->logUploadError($request, 'PostTooLargeException', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'content_length' => $request->header('Content-Length'),
                'php_limits' => $this->getPHPLimits(),
                'request_data' => [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'has_files' => $request->hasFile('archivo'),
                    'user_agent' => $request->userAgent(),
                ]
            ]);
            
            // Retornar respuesta JSON con detalles del error
            return response()->json([
                'success' => false,
                'message' => 'El archivo es demasiado grande para procesar.',
                'error_details' => [
                    'type' => 'PostTooLargeException',
                    'php_post_max_size' => ini_get('post_max_size'),
                    'content_length_mb' => $request->header('Content-Length') ? 
                        round($request->header('Content-Length') / 1024 / 1024, 2) : 'Desconocido',
                    'max_allowed_mb' => $this->convertToMB(ini_get('post_max_size'))
                ],
                'exception' => 'Illuminate\\Http\\Exceptions\\PostTooLargeException',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->toArray()
            ], 413);
            
        } catch (\Exception $e) {
            // Capturar otras excepciones relacionadas con uploads
            if ($this->isUploadRelated($request, $e)) {
                $this->logUploadError($request, 'Upload Exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'content_length' => $request->header('Content-Length'),
                    'php_limits' => $this->getPHPLimits(),
                ]);
            }
            
            // Re-lanzar la excepción para que sea manejada por el handler principal
            throw $e;
        }
    }
    
    /**
     * Loggear errores de upload con contexto detallado
     */
    private function logUploadError(Request $request, string $errorType, array $context = [])
    {
        Log::error("🚨 ERROR DE UPLOAD: {$errorType}", array_merge([
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::check() ? Auth::id() : 'guest',
            'session_id' => session()->getId(),
            'ip' => $request->ip(),
        ], $context));
        
        // También loggear en un archivo específico para uploads
        Log::channel('single')->error("UPLOAD_ERROR: {$errorType}", $context);
    }
    
    /**
     * Obtener límites PHP actuales
     */
    private function getPHPLimits(): array
    {
        return [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ];
    }
    
    /**
     * Convertir tamaño a MB
     */
    private function convertToMB(string $size): string
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);
        
        switch ($unit) {
            case 'G': return ($value * 1024) . 'MB';
            case 'M': return $value . 'MB';
            case 'K': return round($value / 1024, 2) . 'MB';
            default: return round($value / 1024 / 1024, 2) . 'MB';
        }
    }
    
    /**
     * Determinar si la excepción está relacionada con uploads
     */
    private function isUploadRelated(Request $request, \Exception $e): bool
    {
        // Si la request tiene archivos
        if ($request->hasFile('archivo')) {
            return true;
        }
        
        // Si la URL es de upload
        if (str_contains($request->url(), 'upload')) {
            return true;
        }
        
        // Si la excepción menciona upload/file/post size
        $message = strtolower($e->getMessage());
        $uploadKeywords = ['upload', 'file', 'post', 'size', 'large', 'too big'];
        
        foreach ($uploadKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
} 