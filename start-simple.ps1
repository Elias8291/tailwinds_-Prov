# Script simple para Laravel con artisan serve
Write-Host "🚀 Iniciando Laravel con almacenamiento local simple..." -ForegroundColor Green

# Asegurar que el enlace simbólico de storage existe
Write-Host "🔗 Creando enlace de storage..." -ForegroundColor Yellow
php artisan storage:link

# Limpiar cache
Write-Host "🧹 Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Crear directorio de documentos si no existe
Write-Host "📁 Asegurando directorios de documentos..." -ForegroundColor Yellow
$storageDir = "storage/app/public/documentos_solicitante"
if (-not (Test-Path $storageDir)) {
    New-Item -ItemType Directory -Path $storageDir -Force
    Write-Host "✅ Directorio creado: $storageDir" -ForegroundColor Green
}

# Iniciar servidor normal
Write-Host "✅ Iniciando servidor Laravel..." -ForegroundColor Green
Write-Host "📁 Archivos se guardan en: storage/app/public/documentos_solicitante/" -ForegroundColor Cyan
Write-Host "🌐 Servidor disponible en: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "⚠️  Para detener el servidor presiona Ctrl+C" -ForegroundColor Yellow
Write-Host "📦 Los archivos se guardan localmente SIN configuraciones especiales" -ForegroundColor Magenta
Write-Host ""

php artisan serve --port=8000 