# Script de diagnóstico específico para Error 413 (Content Too Large)
Write-Host "🔍 DIAGNÓSTICO COMPLETO - Error 413" -ForegroundColor Red
Write-Host "===================================" -ForegroundColor Red
Write-Host ""

# Función para convertir bytes a formato legible
function Format-Bytes {
    param([long]$bytes)
    if ($bytes -gt 1GB) { return "{0:N2} GB" -f ($bytes / 1GB) }
    elseif ($bytes -gt 1MB) { return "{0:N2} MB" -f ($bytes / 1MB) }
    elseif ($bytes -gt 1KB) { return "{0:N2} KB" -f ($bytes / 1KB) }
    else { return "$bytes bytes" }
}

# 1. Verificar configuración de archivos
Write-Host "📁 1. ARCHIVOS DE CONFIGURACIÓN" -ForegroundColor Yellow
Write-Host "================================" -ForegroundColor Yellow

$configFiles = @{
    "php-dev.ini" = "Configuración PHP para desarrollo"
    "public/.htaccess" = "Configuración Apache"
    "app/Http/Middleware/HandleLargeUploads.php" = "Middleware Laravel"
}

foreach ($file in $configFiles.Keys) {
    if (Test-Path $file) {
        Write-Host "✅ $file - $($configFiles[$file])" -ForegroundColor Green
    } else {
        Write-Host "❌ $file - FALTANTE" -ForegroundColor Red
    }
}

Write-Host ""

# 2. Verificar contenido específico de .htaccess
Write-Host "🔧 2. CONFIGURACIÓN APACHE (.htaccess)" -ForegroundColor Yellow
Write-Host "======================================" -ForegroundColor Yellow

if (Test-Path "public/.htaccess") {
    $htaccess = Get-Content "public/.htaccess" -Raw
    
    # Verificar configuraciones críticas
    $checks = @{
        "upload_max_filesize" = $htaccess -match "upload_max_filesize.*100M"
        "post_max_size" = $htaccess -match "post_max_size.*110M"
        "LimitRequestBody" = $htaccess -match "LimitRequestBody.*104857600"
        "memory_limit" = $htaccess -match "memory_limit.*512M"
    }
    
    foreach ($check in $checks.Keys) {
        if ($checks[$check]) {
            Write-Host "✅ $check configurado correctamente" -ForegroundColor Green
        } else {
            Write-Host "❌ $check NO configurado o incorrecto" -ForegroundColor Red
        }
    }
} else {
    Write-Host "❌ Archivo .htaccess no encontrado" -ForegroundColor Red
}

Write-Host ""

# 3. Verificar configuración PHP actual
Write-Host "🐘 3. CONFIGURACIÓN PHP ACTUAL" -ForegroundColor Yellow
Write-Host "==============================" -ForegroundColor Yellow

try {
    $phpSettings = @{
        "upload_max_filesize" = (php -r "echo ini_get('upload_max_filesize');")
        "post_max_size" = (php -r "echo ini_get('post_max_size');")
        "memory_limit" = (php -r "echo ini_get('memory_limit');")
        "max_execution_time" = (php -r "echo ini_get('max_execution_time');")
        "max_input_time" = (php -r "echo ini_get('max_input_time');")
        "file_uploads" = (php -r "echo ini_get('file_uploads') ? 'On' : 'Off';")
    }
    
    foreach ($setting in $phpSettings.Keys) {
        $value = $phpSettings[$setting]
        
        # Análisis específico por configuración
        $status = "📝"
        $color = "White"
        
        switch ($setting) {
            "upload_max_filesize" {
                if ($value -match "100M|1000M") { $status = "✅"; $color = "Green" }
                elseif ($value -match "^\d+M" -and [int]($value -replace "M","") -ge 50) { $status = "⚠️"; $color = "Yellow" }
                else { $status = "❌"; $color = "Red" }
            }
            "post_max_size" {
                if ($value -match "100M|110M|1000M") { $status = "✅"; $color = "Green" }
                elseif ($value -match "^\d+M" -and [int]($value -replace "M","") -ge 60) { $status = "⚠️"; $color = "Yellow" }
                else { $status = "❌"; $color = "Red" }
            }
            "memory_limit" {
                if ($value -match "512M|1000M|-1") { $status = "✅"; $color = "Green" }
                elseif ($value -match "256M") { $status = "⚠️"; $color = "Yellow" }
                else { $status = "❌"; $color = "Red" }
            }
            "file_uploads" {
                if ($value -eq "On") { $status = "✅"; $color = "Green" }
                else { $status = "❌"; $color = "Red" }
            }
        }
        
        Write-Host "$status $setting = $value" -ForegroundColor $color
    }
    
} catch {
    Write-Host "❌ Error al obtener configuración PHP" -ForegroundColor Red
}

Write-Host ""

# 4. Verificar servidor en ejecución
Write-Host "🌐 4. ESTADO DEL SERVIDOR" -ForegroundColor Yellow
Write-Host "=========================" -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 3 -ErrorAction Stop
    Write-Host "✅ Servidor Laravel corriendo en puerto 8000" -ForegroundColor Green
    
    # Intentar verificar configuración remota
    try {
        $configResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/php-config" -TimeoutSec 3 -ErrorAction Stop
        Write-Host "✅ Configuración PHP accesible en /php-config" -ForegroundColor Green
    } catch {
        Write-Host "⚠️  Endpoint /php-config no disponible" -ForegroundColor Yellow
    }
    
} catch {
    Write-Host "❌ Servidor NO está corriendo o no accesible" -ForegroundColor Red
    Write-Host "💡 Ejecuta: .\start-dev.ps1" -ForegroundColor Cyan
}

Write-Host ""

# 5. Verificar proceso PHP
Write-Host "⚙️  5. PROCESOS Y COMANDOS" -ForegroundColor Yellow
Write-Host "==========================" -ForegroundColor Yellow

# Buscar procesos PHP
$phpProcesses = Get-Process | Where-Object { $_.ProcessName -like "*php*" }
if ($phpProcesses.Count -gt 0) {
    Write-Host "✅ Procesos PHP encontrados:" -ForegroundColor Green
    foreach ($proc in $phpProcesses) {
        Write-Host "   - $($proc.ProcessName) (PID: $($proc.Id))" -ForegroundColor White
    }
} else {
    Write-Host "⚠️  No se encontraron procesos PHP activos" -ForegroundColor Yellow
}

Write-Host ""

# 6. Sugerencias específicas para Error 413
Write-Host "🚨 6. SOLUCIONES PARA ERROR 413" -ForegroundColor Red
Write-Host "===============================" -ForegroundColor Red

Write-Host ""
Write-Host "🛑 PASOS CRÍTICOS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. DETENER servidor actual:" -ForegroundColor White
Write-Host "   Ctrl+C en la ventana donde corre el servidor" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. VERIFICAR que usas el comando correcto:" -ForegroundColor White
Write-Host "   ❌ php artisan serve" -ForegroundColor Red
Write-Host "   ✅ .\start-dev.ps1" -ForegroundColor Green
Write-Host ""
Write-Host "3. REINICIAR con configuración correcta:" -ForegroundColor White
Write-Host "   .\start-dev.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. BUSCAR este mensaje al iniciar:" -ForegroundColor White
Write-Host "   'Límites: upload_max_filesize=100M, post_max_size=100M'" -ForegroundColor Green
Write-Host ""

# 7. Cálculo de límites
Write-Host "📊 7. LÍMITES CALCULADOS" -ForegroundColor Yellow
Write-Host "========================" -ForegroundColor Yellow

$limites = @{
    "Apache LimitRequestBody" = 104857600  # 100MB
    "PHP upload_max_filesize" = 104857600  # 100MB
    "PHP post_max_size" = 115343360        # 110MB
}

foreach ($limite in $limites.Keys) {
    $bytes = $limites[$limite]
    $formatted = Format-Bytes $bytes
    Write-Host "📏 $limite = $formatted" -ForegroundColor Cyan
}

Write-Host ""

# 8. Comandos de prueba
Write-Host "🧪 8. COMANDOS DE PRUEBA" -ForegroundColor Yellow
Write-Host "========================" -ForegroundColor Yellow

Write-Host "Ejecuta estos comandos para verificar:" -ForegroundColor White
Write-Host ""
Write-Host "Verificar configuración PHP:" -ForegroundColor Cyan
Write-Host "php -c php-dev.ini -r \"echo 'upload_max_filesize: '.ini_get('upload_max_filesize').PHP_EOL;\"" -ForegroundColor Gray
Write-Host ""
Write-Host "Verificar servidor:" -ForegroundColor Cyan
Write-Host "curl -I http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host ""

Write-Host "🎯 CAUSA MÁS PROBABLE DEL ERROR 413:" -ForegroundColor Red
Write-Host "=====================================" -ForegroundColor Red
Write-Host "No estás usando .\start-dev.ps1 para iniciar el servidor" -ForegroundColor Yellow
Write-Host "El servidor está corriendo con configuración PHP por defecto" -ForegroundColor Yellow
Write-Host ""

Write-Host "✅ SOLUCIÓN RÁPIDA:" -ForegroundColor Green
Write-Host "==================" -ForegroundColor Green
Write-Host "1. Ctrl+C para detener servidor" -ForegroundColor White
Write-Host "2. .\start-dev.ps1" -ForegroundColor White
Write-Host "3. Buscar mensaje de confirmación" -ForegroundColor White
Write-Host "4. Probar subir archivo otra vez" -ForegroundColor White

Write-Host ""
Read-Host "Presiona Enter para continuar" 