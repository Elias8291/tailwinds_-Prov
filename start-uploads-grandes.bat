@echo off
echo.
echo ==========================================
echo INICIANDO LARAVEL PARA UPLOADS GRANDES
echo ==========================================
echo.

REM Verificar que estamos en el directorio correcto
if not exist artisan (
    echo ERROR: No se encontro el archivo artisan
    echo Ejecutar desde la raiz del proyecto Laravel
    pause
    exit /b 1
)

REM Verificar que existe php-dev.ini
if not exist php-dev.ini (
    echo ERROR: No se encontro php-dev.ini
    echo Verificar que el archivo existe en la raiz del proyecto
    pause
    exit /b 1
)

echo Configuracion que se aplicara:
echo   upload_max_filesize = 100M
echo   post_max_size = 100M  
echo   memory_limit = 512M
echo   max_execution_time = 300
echo.

REM Determinar puerto (por defecto 8000)
set PORT=8000
if not "%1"=="" set PORT=%1

echo Iniciando servidor en http://localhost:%PORT%
echo Presiona Ctrl+C para detener el servidor
echo.
echo Ejecutando: php -c php-dev.ini artisan serve --port=%PORT%
echo.

REM Ejecutar el servidor con configuración personalizada
php -c php-dev.ini artisan serve --port=%PORT%

if errorlevel 1 (
    echo.
    echo ERROR: No se pudo iniciar el servidor
    echo.
    echo Soluciones:
    echo 1. Verificar que PHP este en el PATH
    echo 2. Ejecutar manualmente: php -c php-dev.ini artisan serve
    echo 3. Usar: php artisan serve
    echo.
    pause
) 