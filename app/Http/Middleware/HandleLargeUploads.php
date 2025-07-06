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
        // Log el estado inicial de PHP
        $limitesAnteriores = $this->getPHPLimits();
        
        // Configurar límites PHP de forma más agresiva
        $this->configurarLimitesAgresivo();
        
        // Log el estado después de configurar
        $limitesNuevos = $this->getPHPLimits();
        
        // Log detallado del middleware
        Log::info('🔧 HandleLargeUploads middleware ejecutándose', [
            'url' => $request->url(),
            'method' => $request->method(),
            'has_files' => $request->hasFile('archivo'),
            'content_length' => $request->header('Content-Length'),
            'content_length_mb' => $request->header('Content-Length') ? 
                round($request->header('Content-Length') / 1024 / 1024, 2) : null,
            'limites_anteriores' => $limitesAnteriores,
            'limites_nuevos' => $limitesNuevos,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        // Verificar si el archivo es demasiado grande ANTES de procesar
        $contentLength = $request->header('Content-Length');
        if ($contentLength && $contentLength > (100 * 1024 * 1024)) {
            Log::error('❌ Archivo demasiado grande detectado por HandleLargeUploads', [
                'content_length' => $contentLength,
                'content_length_mb' => round($contentLength / 1024 / 1024, 2),
                'max_allowed_mb' => 100,
                'url' => $request->url(),
                'php_limits_actuales' => $this->getPHPLimits()
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'El archivo es demasiado grande. Máximo permitido: 100MB',
                'tamaño_detectado' => round($contentLength / 1024 / 1024, 2) . 'MB',
                'max_permitido' => '100MB',
                'error_source' => 'HandleLargeUploads middleware'
            ], 413);
        }

        // Intentar configurar límites una vez más justo antes de continuar
        $this->configurarLimitesUltimoIntento();

        return $next($request);
    }
    
    /**
     * Configurar límites PHP de forma más agresiva con múltiples intentos
     */
    private function configurarLimitesAgresivo()
    {
        // Intentar múltiples enfoques para configurar límites
        $configs = [
            'upload_max_filesize' => '100M',
            'post_max_size' => '110M',
            'memory_limit' => '512M',
            'max_execution_time' => '300',
            'max_input_time' => '300',
            'max_file_uploads' => '20',
            'max_input_vars' => '5000'
        ];
        
        foreach ($configs as $key => $value) {
            $antes = ini_get($key);
            
            // Intentar configurar múltiples veces para asegurar que se aplique
            for ($i = 0; $i < 3; $i++) {
                ini_set($key, $value);
            }
            
            $despues = ini_get($key);
            
            if ($antes !== $despues) {
                Log::info("✅ Configuración cambiada: $key de '$antes' a '$despues'");
            } else {
                Log::warning("⚠️ No se pudo cambiar: $key (permanece en '$antes')");
                
                // Intentar enfoques alternativos para ciertos valores críticos
                if ($key === 'post_max_size' || $key === 'upload_max_filesize') {
                    $this->intentarConfiguracionAlternativa($key, $value);
                }
            }
        }
    }
    
    /**
     * Último intento de configuración justo antes de procesar
     */
    private function configurarLimitesUltimoIntento()
    {
        // Verificar y reconfigurar los valores más críticos
        $criticalSettings = [
            'upload_max_filesize' => '100M',
            'post_max_size' => '110M'
        ];
        
        foreach ($criticalSettings as $key => $value) {
            $current = ini_get($key);
            $expectedBytes = $this->convertToBytes($value);
            $currentBytes = $this->convertToBytes($current);
            
            if ($currentBytes < $expectedBytes) {
                Log::warning("🔄 Reintentando configuración crítica: $key");
                ini_set($key, $value);
                
                $newValue = ini_get($key);
                Log::info("🔧 Reconfiguración $key: $current -> $newValue");
            }
        }
    }
    
    /**
     * Intentar configuración alternativa para valores críticos
     */
    private function intentarConfiguracionAlternativa(string $key, string $value)
    {
        Log::info("🔄 Intentando configuración alternativa para $key");
        
        // Intentar diferentes formatos
        $alternativeFormats = [
            $value,
            strtolower($value),
            str_replace('M', 'm', $value),
            $this->convertToBytes($value) // Como número puro
        ];
        
        foreach ($alternativeFormats as $format) {
            ini_set($key, $format);
            $result = ini_get($key);
            
            if ($result !== ini_get($key)) {
                Log::info("✅ Configuración alternativa exitosa: $key = $format (resultado: $result)");
                break;
            }
        }
    }
    
    /**
     * Convertir tamaño a bytes
     */
    private function convertToBytes(string $size): int
    {
        $size = trim($size);
        $unit = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);
        
        switch ($unit) {
            case 'G': return $value * 1024 * 1024 * 1024;
            case 'M': return $value * 1024 * 1024;
            case 'K': return $value * 1024;
            default: return (int) $size;
        }
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
            'max_input_vars' => ini_get('max_input_vars'),
            'max_input_time' => ini_get('max_input_time'),
        ];
    }
} 