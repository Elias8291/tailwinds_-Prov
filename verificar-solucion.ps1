# Script para verificar que la solución del Error 413 se aplicó correctamente
Write-Host "🔍 VERIFICANDO SOLUCIÓN ERROR 413" -ForegroundColor Green
Write-Host "===================================" -ForegroundColor Green
Write-Host ""

# 1. Verificar archivos de configuración
Write-Host "📁 1. Verificando archivos de configuración..." -ForegroundColor Yellow

$configFiles = @{
    "php-dev.ini" = "50M"
    "public/.htaccess" = "LimitRequestBody 62914560"
    "SOLUCION_413.md" = "Documentación de la solución"
}

foreach ($file in $configFiles.Keys) {
    if (Test-Path $file) {
        Write-Host "✅ $file encontrado" -ForegroundColor Green
        
        # Verificar contenido específico
        if ($file -eq "php-dev.ini") {
            $content = Get-Content $file -Raw
            if ($content -match "upload_max_filesize = 50M") {
                Write-Host "   ✅ upload_max_filesize = 50M configurado" -ForegroundColor Green
            } else {
                Write-Host "   ❌ upload_max_filesize NO configurado correctamente" -ForegroundColor Red
            }
        }
        
        if ($file -eq "public/.htaccess") {
            $content = Get-Content $file -Raw
            if ($content -match "LimitRequestBody 62914560") {
                Write-Host "   ✅ LimitRequestBody = 60MB configurado" -ForegroundColor Green
            } else {
                Write-Host "   ❌ LimitRequestBody NO configurado correctamente" -ForegroundColor Red
            }
        }
    } else {
        Write-Host "❌ $file NO encontrado" -ForegroundColor Red
    }
}

Write-Host ""

# 2. Verificar configuración PHP actual
Write-Host "🐘 2. Verificando configuración PHP actual..." -ForegroundColor Yellow

try {
    $uploadMax = php -c php-dev.ini -r "echo ini_get('upload_max_filesize');"
    $postMax = php -c php-dev.ini -r "echo ini_get('post_max_size');"
    
    if ($uploadMax -eq "50M") {
        Write-Host "✅ upload_max_filesize = $uploadMax (CORRECTO)" -ForegroundColor Green
    } else {
        Write-Host "❌ upload_max_filesize = $uploadMax (DEBERÍA SER 50M)" -ForegroundColor Red
    }
    
    if ($postMax -eq "60M") {
        Write-Host "✅ post_max_size = $postMax (CORRECTO)" -ForegroundColor Green
    } else {
        Write-Host "❌ post_max_size = $postMax (DEBERÍA SER 60M)" -ForegroundColor Red
    }
    
} catch {
    Write-Host "❌ Error al verificar configuración PHP" -ForegroundColor Red
}

Write-Host ""

# 3. Verificar estado del servidor
Write-Host "🌐 3. Verificando servidor..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 3 -ErrorAction Stop
    Write-Host "✅ Servidor corriendo en puerto 8000" -ForegroundColor Green
    
    # Intentar acceder a configuración PHP
    try {
        $phpConfig = Invoke-WebRequest -Uri "http://127.0.0.1:8000/php-config" -TimeoutSec 3 -ErrorAction Stop
        Write-Host "✅ Configuración PHP accesible en: http://127.0.0.1:8000/php-config" -ForegroundColor Green
    } catch {
        Write-Host "⚠️  No se pudo acceder a /php-config" -ForegroundColor Yellow
    }
    
} catch {
    Write-Host "❌ Servidor NO está corriendo" -ForegroundColor Red
    Write-Host "💡 Ejecuta: .\start-dev.ps1" -ForegroundColor Yellow
}

Write-Host ""

# 4. Resumen de la solución
Write-Host "📊 4. RESUMEN DE LA SOLUCIÓN" -ForegroundColor Yellow
Write-Host "============================" -ForegroundColor Yellow

Write-Host "📝 Tu archivo de 8.7MB ahora debería subir correctamente" -ForegroundColor White
Write-Host "📏 Nuevos límites (todos consistentes):" -ForegroundColor White
Write-Host "   • Laravel validation: 50MB" -ForegroundColor Cyan
Write-Host "   • PHP upload_max_filesize: 50MB" -ForegroundColor Cyan  
Write-Host "   • PHP post_max_size: 60MB" -ForegroundColor Cyan
Write-Host "   • Apache LimitRequestBody: 60MB" -ForegroundColor Cyan
Write-Host "   • JavaScript validation: 50MB" -ForegroundColor Cyan

Write-Host ""

# 5. Instrucciones finales
Write-Host "🚀 5. PASOS SIGUIENTES" -ForegroundColor Yellow
Write-Host "======================" -ForegroundColor Yellow

Write-Host "1. Si el servidor NO está corriendo:" -ForegroundColor White
Write-Host "   .\start-dev.ps1" -ForegroundColor Cyan

Write-Host ""
Write-Host "2. Verifica que veas este mensaje al iniciar:" -ForegroundColor White
Write-Host "   'Límites: upload_max_filesize=50M, post_max_size=60M'" -ForegroundColor Green

Write-Host ""
Write-Host "3. Prueba subir tu archivo de 8.7MB:" -ForegroundColor White
Write-Host "   • Ve al formulario de documentos" -ForegroundColor Cyan
Write-Host "   • Selecciona tu archivo PDF de 8.7MB" -ForegroundColor Cyan
Write-Host "   • Debería subir SIN error 413" -ForegroundColor Cyan

Write-Host ""
Write-Host "🎯 Si todo está en verde arriba, el problema está SOLUCIONADO" -ForegroundColor Green

Write-Host ""
Read-Host "Presiona Enter para continuar" 