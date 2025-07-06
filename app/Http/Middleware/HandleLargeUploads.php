<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleLargeUploads
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
        // Log que el middleware se está ejecutando
        Log::info('🔧 HandleLargeUploads middleware ejecutándose', [
            'url' => $request->url(),
            'method' => $request->method(),
            'has_files' => $request->hasFile('archivo'),
            'content_length' => $request->header('Content-Length'),
        ]);

        // Mostrar valores actuales ANTES de cambiar
        Log::info('📊 Valores PHP ANTES:', [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ]);

        // Configurar límites dinámicamente para uploads
        if ($request->hasFile('archivo') || str_contains($request->url(), 'upload-documento')) {
            Log::info('🚀 Configurando límites para archivos grandes...');
            
            ini_set('upload_max_filesize', '50M');
            ini_set('post_max_size', '60M');
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);
            ini_set('max_input_time', 300);
            
            // Verificar que se aplicaron los cambios
            Log::info('📊 Valores PHP DESPUÉS:', [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ]);
        }

        return $next($request);
    }
} 