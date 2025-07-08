# PowerShell script to run database seeder with increased memory limits
# This script runs the seeder with optimized PHP settings for large datasets

Write-Host "🚀 Iniciando seeder con configuración optimizada..." -ForegroundColor Green

# Check if we're in the correct directory
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel." -ForegroundColor Red
    exit 1
}

# Check if the custom PHP config file exists
if (-not (Test-Path "php-seeder.ini")) {
    Write-Host "❌ Error: No se encontró el archivo php-seeder.ini" -ForegroundColor Red
    exit 1
}

try {
    # Run the seeder with custom PHP configuration
    Write-Host "📊 Ejecutando seeder con límites de memoria aumentados..." -ForegroundColor Yellow
    
    # Use the custom PHP configuration file
    php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Seeder ejecutado exitosamente!" -ForegroundColor Green
    } else {
        Write-Host "❌ Error al ejecutar el seeder" -ForegroundColor Red
        exit 1
    }
    
} catch {
    Write-Host "❌ Error inesperado: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "🎉 Proceso completado!" -ForegroundColor Green 