# Script PowerShell para desarrollo local
Write-Host "🚀 Iniciando aplicación Laravel en modo desarrollo..." -ForegroundColor Green

# Verificar si existe la configuración PHP personalizada
if (-not (Test-Path "php-dev.ini")) {
    Write-Host "❌ No se encontró php-dev.ini" -ForegroundColor Red
    Read-Host "Presiona Enter para continuar"
    exit 1
}

# Limpiar cache de desarrollo
Write-Host "🧹 Limpiando cache de desarrollo..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Iniciar servidor con configuración PHP personalizada
Write-Host "✅ Iniciando servidor con configuración PHP optimizada para archivos grandes..." -ForegroundColor Green
Write-Host "📁 Límites: upload_max_filesize=100M, post_max_size=100M" -ForegroundColor Cyan
Write-Host "🌐 Servidor disponible en: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "⚠️  Para detener el servidor presiona Ctrl+C" -ForegroundColor Yellow
Write-Host ""

php -c php-dev.ini artisan serve --port=8000 