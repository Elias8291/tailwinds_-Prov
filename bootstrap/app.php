<?php

// ============================================================================
// CONFIGURACIÓN TEMPRANA PARA ARCHIVOS GRANDES
// ============================================================================
// Configurar límites antes de que Laravel inicie completamente
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '110M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);
ini_set('max_file_uploads', 20);

// Log de configuración inicial
if (function_exists('error_log')) {
    error_log('🚀 Bootstrap: Límites de archivos configurados - upload_max_filesize: ' . ini_get('upload_max_filesize') . ', post_max_size: ' . ini_get('post_max_size'));
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
