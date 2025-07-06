# Script para verificar configuración PHP
Write-Host "🔍 Verificando configuración PHP para archivos grandes..." -ForegroundColor Cyan

# Verificar si el archivo php-dev.ini existe
if (-not (Test-Path "php-dev.ini")) {
    Write-Host "❌ Error: No se encontró php-dev.ini" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Archivo php-dev.ini encontrado" -ForegroundColor Green

# Mostrar configuración actual
Write-Host "`n📋 Configuración PHP actual:" -ForegroundColor Yellow
php -c php-dev.ini -r "
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;
echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;
echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;
echo 'max_execution_time: ' . ini_get('max_execution_time') . PHP_EOL;
echo 'max_input_time: ' . ini_get('max_input_time') . PHP_EOL;
"

# Verificar valores mínimos requeridos
Write-Host "`n🔧 Verificando valores mínimos..." -ForegroundColor Yellow

$upload_max = php -c php-dev.ini -r "echo ini_get('upload_max_filesize');"
$post_max = php -c php-dev.ini -r "echo ini_get('post_max_size');"

if ($upload_max -match "100M") {
    Write-Host "✅ upload_max_filesize: $upload_max (OK)" -ForegroundColor Green
} else {
    Write-Host "❌ upload_max_filesize: $upload_max (Debería ser 100M)" -ForegroundColor Red
}

if ($post_max -match "100M") {
    Write-Host "✅ post_max_size: $post_max (OK)" -ForegroundColor Green
} else {
    Write-Host "❌ post_max_size: $post_max (Debería ser 100M)" -ForegroundColor Red
}

Write-Host "`n💡 Para aplicar esta configuración:" -ForegroundColor Cyan
Write-Host "   1. Detén el servidor actual (Ctrl+C)" -ForegroundColor White
Write-Host "   2. Ejecuta: .\start-dev.ps1" -ForegroundColor White
Write-Host "   3. Verifica que veas: 'Límites: upload_max_filesize=100M'" -ForegroundColor White

Write-Host "`n🌐 Servidor Laravel debería iniciarse con:" -ForegroundColor Cyan
Write-Host "   php -c php-dev.ini artisan serve --port=8000" -ForegroundColor White 