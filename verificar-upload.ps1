# Script para verificar y solucionar problemas de upload (Error 413)
Write-Host "🔍 Verificando configuración para uploads grandes..." -ForegroundColor Cyan
Write-Host ""

# Verificar archivos de configuración
Write-Host "📋 Verificando archivos de configuración:" -ForegroundColor Yellow
if (Test-Path "php-dev.ini") {
    Write-Host "✅ php-dev.ini encontrado" -ForegroundColor Green
} else {
    Write-Host "❌ php-dev.ini NO encontrado" -ForegroundColor Red
}

if (Test-Path "public/.htaccess") {
    Write-Host "✅ public/.htaccess encontrado" -ForegroundColor Green
    
    # Verificar si contiene LimitRequestBody
    $htaccessContent = Get-Content "public/.htaccess" -Raw
    if ($htaccessContent -match "LimitRequestBody") {
        Write-Host "✅ LimitRequestBody configurado en .htaccess" -ForegroundColor Green
    } else {
        Write-Host "❌ LimitRequestBody NO configurado en .htaccess" -ForegroundColor Red
        Write-Host "💡 Se necesita añadir: LimitRequestBody 104857600" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ public/.htaccess NO encontrado" -ForegroundColor Red
}

Write-Host ""

# Verificar configuración actual de PHP
Write-Host "📊 Configuración actual de PHP:" -ForegroundColor Yellow
$phpConfig = @{
    "upload_max_filesize" = (php -r "echo ini_get('upload_max_filesize');")
    "post_max_size" = (php -r "echo ini_get('post_max_size');")
    "memory_limit" = (php -r "echo ini_get('memory_limit');")
    "max_execution_time" = (php -r "echo ini_get('max_execution_time');")
}

foreach ($key in $phpConfig.Keys) {
    $value = $phpConfig[$key]
    if ($key -eq "upload_max_filesize" -or $key -eq "post_max_size") {
        if ($value -match "100M|110M") {
            Write-Host "✅ $key = $value" -ForegroundColor Green
        } else {
            Write-Host "❌ $key = $value (debería ser 100M+)" -ForegroundColor Red
        }
    } elseif ($key -eq "memory_limit") {
        if ($value -match "512M") {
            Write-Host "✅ $key = $value" -ForegroundColor Green
        } else {
            Write-Host "⚠️  $key = $value (recomendado: 512M)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "📝 $key = $value" -ForegroundColor White
    }
}

Write-Host ""

# Instrucciones para solucionar error 413
Write-Host "🚨 Para solucionar el Error 413:" -ForegroundColor Red
Write-Host ""
Write-Host "1. 🛑 DETÉN el servidor actual (Ctrl+C)" -ForegroundColor Yellow
Write-Host ""
Write-Host "2. 🚀 REINICIA usando el script correcto:" -ForegroundColor Yellow
Write-Host "   .\start-dev.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. ✅ Verifica que veas este mensaje:" -ForegroundColor Yellow
Write-Host "   'Límites: upload_max_filesize=100M, post_max_size=100M'" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. 🧪 Prueba subir un archivo menor a 100MB" -ForegroundColor Yellow
Write-Host ""

# Verificar si el servidor está corriendo con configuración correcta
Write-Host "🔍 Verificando si el servidor usa configuración correcta..." -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✅ Servidor está corriendo en puerto 8000" -ForegroundColor Green
    
    # Verificar URL de configuración PHP
    try {
        $phpInfo = Invoke-WebRequest -Uri "http://127.0.0.1:8000/php-config" -TimeoutSec 5 -ErrorAction Stop
        Write-Host "✅ Puedes verificar configuración en: http://127.0.0.1:8000/php-config" -ForegroundColor Green
    } catch {
        Write-Host "⚠️  No se pudo acceder a php-config, pero el servidor está corriendo" -ForegroundColor Yellow
    }
    
} catch {
    Write-Host "❌ Servidor NO está corriendo en puerto 8000" -ForegroundColor Red
    Write-Host "💡 Ejecuta: .\start-dev.ps1" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "📋 Resumen de límites configurados:" -ForegroundColor Cyan
Write-Host "   • Archivos hasta: 100MB" -ForegroundColor White
Write-Host "   • POST hasta: 110MB" -ForegroundColor White  
Write-Host "   • Memoria: 512MB" -ForegroundColor White
Write-Host "   • Tiempo ejecución: 300s" -ForegroundColor White
Write-Host "   • Apache LimitRequestBody: 100MB" -ForegroundColor White
Write-Host ""

Write-Host "💡 Si el error persiste:" -ForegroundColor Yellow
Write-Host "   1. Verifica que el archivo sea realmente menor a 100MB" -ForegroundColor White
Write-Host "   2. Prueba con un archivo más pequeño primero (ej: 10MB)" -ForegroundColor White
Write-Host "   3. Revisa los logs en storage/logs/laravel.log" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para continuar" 