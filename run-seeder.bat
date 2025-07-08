@echo off
REM Batch script to run database seeder with increased memory limits
REM This script runs the seeder with optimized PHP settings for large datasets

echo 🚀 Iniciando seeder con configuración optimizada...

REM Check if we're in the correct directory
if not exist "artisan" (
    echo ❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel.
    pause
    exit /b 1
)

REM Check if the custom PHP config file exists
if not exist "php-seeder.ini" (
    echo ❌ Error: No se encontró el archivo php-seeder.ini
    pause
    exit /b 1
)

echo 📊 Ejecutando seeder con límites de memoria aumentados...

REM Use the custom PHP configuration file
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder

if %ERRORLEVEL% EQU 0 (
    echo ✅ Seeder ejecutado exitosamente!
) else (
    echo ❌ Error al ejecutar el seeder
    pause
    exit /b 1
)

echo 🎉 Proceso completado!
pause 