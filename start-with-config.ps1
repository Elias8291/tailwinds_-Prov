# Script para iniciar Laravel con configuración de uploads grandes
# Ejecutar: PowerShell -ExecutionPolicy Bypass -File start-with-config.ps1

Write-Host "🚀 INICIANDO LARAVEL CON CONFIGURACIÓN PARA UPLOADS GRANDES" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: No se encontró el archivo artisan. Ejecutar desde la raíz del proyecto." -ForegroundColor Red
    exit 1
}

# Verificar que existe php-dev.ini
if (-not (Test-Path "php-dev.ini")) {
    Write-Host "❌ Error: No se encontró php-dev.ini" -ForegroundColor Red
    exit 1
}

Write-Host "📋 Configuración que se aplicará:" -ForegroundColor Cyan
Write-Host "   upload_max_filesize = 100M" -ForegroundColor White
Write-Host "   post_max_size = 100M" -ForegroundColor White
Write-Host "   memory_limit = 512M" -ForegroundColor White
Write-Host "   max_execution_time = 300" -ForegroundColor White
Write-Host ""

# Determinar el puerto
$puerto = 8000
if ($args.Length -gt 0) {
    $puerto = $args[0]
}

Write-Host "🌐 Iniciando servidor en http://localhost:$puerto" -ForegroundColor Green
Write-Host "   Presiona Ctrl+C para detener el servidor" -ForegroundColor Yellow
Write-Host ""

# Crear comando con configuración personalizada
$comando = "php -c php-dev.ini artisan serve --port=$puerto"

Write-Host "🔧 Ejecutando: $comando" -ForegroundColor Gray
Write-Host ""

# Ejecutar el servidor
try {
    Invoke-Expression $comando
} catch {
    Write-Host "❌ Error al iniciar el servidor: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "💡 Soluciones alternativas:" -ForegroundColor Cyan
    Write-Host "1. Verificar que PHP esté en el PATH" -ForegroundColor White
    Write-Host "2. Ejecutar manualmente: php -c php-dev.ini artisan serve" -ForegroundColor White
    Write-Host "3. Usar el servidor incluido: php artisan serve" -ForegroundColor White
} 