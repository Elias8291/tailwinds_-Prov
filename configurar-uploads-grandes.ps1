# Script de PowerShell para configurar uploads grandes
# Ejecutar como administrador: PowerShell -ExecutionPolicy Bypass -File configurar-uploads-grandes.ps1

Write-Host "🚀 CONFIGURANDO UPLOADS GRANDES PARA LARAVEL" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

# Verificar si se está ejecutando como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")

if (-not $isAdmin) {
    Write-Host "⚠️  ADVERTENCIA: Se recomienda ejecutar como administrador" -ForegroundColor Yellow
    Write-Host ""
}

# 1. Verificar archivos de configuración
Write-Host "📁 Verificando archivos de configuración..." -ForegroundColor Cyan

$configFiles = @{
    "public\.htaccess" = "Configuración Apache"
    "php-dev.ini" = "Configuración PHP personalizada"
    "storage\app\public\.htaccess" = "Configuración storage"
    "app\Http\Middleware\HandleLargeUploads.php" = "Middleware uploads"
}

foreach ($file in $configFiles.Keys) {
    if (Test-Path $file) {
        Write-Host "✅ $file - $($configFiles[$file])" -ForegroundColor Green
    } else {
        Write-Host "❌ $file - $($configFiles[$file]) (NO EXISTE)" -ForegroundColor Red
    }
}

Write-Host ""

# 2. Ejecutar migración
Write-Host "🗄️  Ejecutando migración de base de datos..." -ForegroundColor Cyan
try {
    & php artisan migrate --force
    Write-Host "✅ Migración ejecutada correctamente" -ForegroundColor Green
} catch {
    Write-Host "❌ Error en migración: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# 3. Verificar symlink de storage
Write-Host "🔗 Verificando symlink de storage..." -ForegroundColor Cyan
if (Test-Path "public\storage") {
    Write-Host "✅ Symlink de storage existe" -ForegroundColor Green
} else {
    Write-Host "🔧 Creando symlink de storage..." -ForegroundColor Yellow
    try {
        & php artisan storage:link
        Write-Host "✅ Symlink creado correctamente" -ForegroundColor Green
    } catch {
        Write-Host "❌ Error creando symlink: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""

# 4. Verificar permisos de directorio
Write-Host "📂 Verificando permisos de directorios..." -ForegroundColor Cyan

$directories = @("storage", "storage\app", "storage\app\public", "storage\logs")

foreach ($dir in $directories) {
    if (Test-Path $dir) {
        try {
            $testFile = Join-Path $dir "test_write.tmp"
            "test" | Out-File -FilePath $testFile -Encoding UTF8
            Remove-Item $testFile -Force
            Write-Host "✅ $dir - Escribible" -ForegroundColor Green
        } catch {
            Write-Host "❌ $dir - No escribible" -ForegroundColor Red
        }
    } else {
        Write-Host "❌ $dir - No existe" -ForegroundColor Red
    }
}

Write-Host ""

# 5. Limpiar caché de Laravel
Write-Host "🧹 Limpiando caché de Laravel..." -ForegroundColor Cyan
try {
    & php artisan config:clear
    & php artisan cache:clear
    & php artisan route:clear
    & php artisan view:clear
    Write-Host "✅ Caché limpiado correctamente" -ForegroundColor Green
} catch {
    Write-Host "❌ Error limpiando caché: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# 6. Verificar configuración PHP
Write-Host "⚙️  Verificando configuración PHP..." -ForegroundColor Cyan
try {
    & php verificar-upload.php
} catch {
    Write-Host "❌ Error verificando configuración: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# 7. Reiniciar servicios (si es administrador)
if ($isAdmin) {
    Write-Host "🔄 Reiniciando servicios web..." -ForegroundColor Cyan
    
    # Intentar reiniciar Apache si está instalado
    try {
        $apacheService = Get-Service -Name "Apache*" -ErrorAction SilentlyContinue
        if ($apacheService) {
            Restart-Service $apacheService.Name -Force
            Write-Host "✅ Apache reiniciado" -ForegroundColor Green
        }
    } catch {
        Write-Host "⚠️  No se pudo reiniciar Apache (puede que no esté instalado)" -ForegroundColor Yellow
    }
    
    # Intentar reiniciar IIS si está disponible
    try {
        if (Get-Command "iisreset" -ErrorAction SilentlyContinue) {
            & iisreset /restart
            Write-Host "✅ IIS reiniciado" -ForegroundColor Green
        }
    } catch {
        Write-Host "⚠️  No se pudo reiniciar IIS" -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  Para reiniciar servicios web, ejecutar como administrador" -ForegroundColor Yellow
}

Write-Host ""

# 8. Instrucciones finales
Write-Host "🎯 CONFIGURACIÓN COMPLETADA" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""
Write-Host "📋 SIGUIENTE PASOS:" -ForegroundColor Cyan
Write-Host "1. Reiniciar el servidor web si no se hizo automáticamente" -ForegroundColor White
Write-Host "2. Probar con un archivo pequeño (< 10MB) primero" -ForegroundColor White
Write-Host "3. Si persiste el problema, revisar logs:" -ForegroundColor White
Write-Host "   - storage\logs\laravel.log" -ForegroundColor Gray
Write-Host "   - Logs del servidor web (Apache/IIS)" -ForegroundColor Gray
Write-Host ""
Write-Host "🔧 COMANDOS ÚTILES:" -ForegroundColor Cyan
Write-Host "   php verificar-upload.php    # Verificar configuración" -ForegroundColor Gray
Write-Host "   php artisan migrate         # Ejecutar migraciones" -ForegroundColor Gray
Write-Host "   php artisan storage:link    # Crear symlink storage" -ForegroundColor Gray
Write-Host ""
Write-Host "✨ ¡La configuración está lista para archivos de hasta 100MB!" -ForegroundColor Green 