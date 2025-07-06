# Script PowerShell con configuración forzada para archivos grandes
Write-Host "🚀 Iniciando Laravel con configuración FORZADA para archivos grandes..." -ForegroundColor Green

# Verificar que PHP esté disponible
if (-not (Get-Command "php" -ErrorAction SilentlyContinue)) {
    Write-Host "❌ PHP no encontrado en PATH" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}

# Mostrar configuración actual de PHP
Write-Host "📊 Configuración PHP actual:" -ForegroundColor Yellow
php -r "
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;
echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;
echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;
echo 'max_execution_time: ' . ini_get('max_execution_time') . PHP_EOL;
"

# Limpiar cache
Write-Host "🧹 Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear --quiet
php artisan route:clear --quiet
php artisan view:clear --quiet

# Crear enlace de storage
Write-Host "🔗 Creando enlace de storage..." -ForegroundColor Yellow
php artisan storage:link --quiet

# Crear directorio de documentos
Write-Host "📁 Asegurando directorios..." -ForegroundColor Yellow
$storageDir = "storage/app/public/documentos_solicitante"
if (-not (Test-Path $storageDir)) {
    New-Item -ItemType Directory -Path $storageDir -Force | Out-Null
    Write-Host "✅ Directorio creado: $storageDir" -ForegroundColor Green
}

# Configurar variables de entorno para PHP
$env:PHP_INI_SCAN_DIR = ""
$phpArgs = @(
    "-d upload_max_filesize=100M",
    "-d post_max_size=110M", 
    "-d memory_limit=512M",
    "-d max_execution_time=300",
    "-d max_input_time=300",
    "-d max_file_uploads=20"
)

Write-Host "🔧 Configuraciones aplicadas:" -ForegroundColor Cyan
foreach ($arg in $phpArgs) {
    Write-Host "   $arg" -ForegroundColor White
}

Write-Host ""
Write-Host "✅ Iniciando servidor con configuración forzada..." -ForegroundColor Green
Write-Host "🌐 Servidor disponible en: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "📁 Archivos se guardan en: storage/app/public/documentos_solicitante/" -ForegroundColor Cyan
Write-Host "⚠️  Para detener el servidor presiona Ctrl+C" -ForegroundColor Yellow
Write-Host "🔥 LÍMITES FORZADOS: upload_max_filesize=100M, post_max_size=110M" -ForegroundColor Magenta
Write-Host ""

# Ejecutar servidor con argumentos PHP específicos
$phpCommand = "php " + ($phpArgs -join " ") + " artisan serve --port=8000"
Invoke-Expression $phpCommand 