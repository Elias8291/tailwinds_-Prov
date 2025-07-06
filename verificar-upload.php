<?php
/**
 * Script para verificar configuración de uploads grandes
 * Ejecutar desde la raíz del proyecto: php verificar-upload.php
 */

echo "🔍 VERIFICACIÓN DE CONFIGURACIÓN PARA UPLOADS GRANDES\n";
echo "================================================\n\n";

// Verificar configuración PHP
echo "📋 CONFIGURACIÓN PHP:\n";
echo "---------------------\n";
$phpConfig = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'file_uploads' => ini_get('file_uploads') ? 'ON' : 'OFF',
];

foreach ($phpConfig as $setting => $value) {
    $status = '';
    
    if ($setting === 'upload_max_filesize') {
        $status = (convertToBytes($value) >= convertToBytes('100M')) ? '✅' : '❌';
    } elseif ($setting === 'post_max_size') {
        $status = (convertToBytes($value) >= convertToBytes('110M')) ? '✅' : '❌';
    } elseif ($setting === 'memory_limit') {
        $status = (convertToBytes($value) >= convertToBytes('512M')) ? '✅' : '❌';
    } elseif ($setting === 'max_execution_time') {
        $status = ($value >= 300) ? '✅' : '❌';
    } elseif ($setting === 'max_input_time') {
        $status = ($value >= 300) ? '✅' : '❌';
    } elseif ($setting === 'file_uploads') {
        $status = ($value === 'ON') ? '✅' : '❌';
    } else {
        $status = '✅';
    }
    
    echo "$status $setting: $value\n";
}

echo "\n";

// Verificar archivos de configuración
echo "📁 ARCHIVOS DE CONFIGURACIÓN:\n";
echo "------------------------------\n";

$configFiles = [
    'php-dev.ini' => 'Configuración PHP personalizada',
    'public/.htaccess' => 'Configuración Apache/Nginx',
    'app/Http/Middleware/HandleLargeUploads.php' => 'Middleware para uploads',
];

foreach ($configFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file - $description\n";
    } else {
        echo "❌ $file - $description (NO EXISTE)\n";
    }
}

echo "\n";

// Verificar Laravel
echo "⚡ CONFIGURACIÓN LARAVEL:\n";
echo "-------------------------\n";

// Verificar .env
if (file_exists('.env')) {
    echo "✅ Archivo .env existe\n";
} else {
    echo "❌ Archivo .env no encontrado\n";
}

// Verificar storage
$storagePath = 'storage/app/public';
if (is_dir($storagePath) && is_writable($storagePath)) {
    echo "✅ Directorio storage escribible\n";
} else {
    echo "❌ Directorio storage no escribible\n";
}

// Verificar symlink
if (is_link('public/storage')) {
    echo "✅ Symlink storage configurado\n";
} else {
    echo "❌ Symlink storage no configurado (ejecutar: php artisan storage:link)\n";
}

echo "\n";

// Verificar base de datos
echo "🗄️ VERIFICACIÓN BASE DE DATOS:\n";
echo "-------------------------------\n";

try {
    // Verificar si existe la migración
    $migrationFiles = glob('database/migrations/*documento_solicitante*');
    if (count($migrationFiles) > 0) {
        echo "✅ Migración documento_solicitante encontrada\n";
        
        // Verificar si existe la nueva migración
        $newMigration = glob('database/migrations/*add_missing_fields_to_documento_solicitante*');
        if (count($newMigration) > 0) {
            echo "✅ Nueva migración para campos faltantes encontrada\n";
            echo "⚠️  EJECUTAR: php artisan migrate\n";
        } else {
            echo "❌ Migración para campos faltantes no encontrada\n";
        }
    } else {
        echo "❌ Migración documento_solicitante no encontrada\n";
    }
} catch (Exception $e) {
    echo "❌ Error verificando base de datos: " . $e->getMessage() . "\n";
}

echo "\n";

// Recomendaciones
echo "💡 RECOMENDACIONES:\n";
echo "-------------------\n";
echo "1. Ejecutar: php artisan migrate\n";
echo "2. Verificar que el servidor web (Apache/Nginx) esté reiniciado\n";
echo "3. Verificar que no hay proxy/CDN limitando uploads\n";
echo "4. Probar con archivo pequeño (< 10MB) primero\n";
echo "5. Revisar logs: storage/logs/laravel.log\n";

echo "\n";
echo "🚀 CONFIGURACIÓN COMPLETADA\n";
echo "Si el problema persiste, revisar logs del servidor web.\n";

function convertToBytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value)-1]);
    $value = (float) $value;
    
    switch($last) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
} 