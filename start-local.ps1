# Script PowerShell para inicio local optimizado
Write-Host "🚀 Iniciando aplicación Laravel en modo local..." -ForegroundColor Green

# Función para encontrar un puerto disponible
function Find-AvailablePort {
    param([int]$StartPort = 8000, [int]$EndPort = 8010)
    
    for ($port = $StartPort; $port -le $EndPort; $port++) {
        try {
            $listener = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Parse("127.0.0.1"), $port)
            $listener.Start()
            $listener.Stop()
            return $port
        }
        catch {
            continue
        }
    }
    return $null
}

# Verificar que PHP esté disponible
if (-not (Get-Command "php" -ErrorAction SilentlyContinue)) {
    Write-Host "❌ PHP no encontrado en PATH" -ForegroundColor Red
    Write-Host "💡 Asegúrate de tener PHP instalado y en el PATH" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit 1
}

# Verificar que estemos en el directorio correcto
if (-not (Test-Path "artisan")) {
    Write-Host "❌ No se encontró el archivo artisan" -ForegroundColor Red
    Write-Host "💡 Asegúrate de estar en el directorio raíz del proyecto Laravel" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit 1
}

# Encontrar puerto disponible
Write-Host "🔍 Buscando puerto disponible..." -ForegroundColor Yellow
$availablePort = Find-AvailablePort
if (-not $availablePort) {
    Write-Host "❌ No se encontró ningún puerto disponible entre 8000-8010" -ForegroundColor Red
    Write-Host "💡 Cierra otras aplicaciones que puedan estar usando estos puertos" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit 1
}

Write-Host "✅ Puerto disponible encontrado: $availablePort" -ForegroundColor Green

# Limpiar cache de desarrollo
Write-Host "🧹 Limpiando cache de desarrollo..." -ForegroundColor Yellow
php artisan config:clear --quiet
php artisan route:clear --quiet
php artisan view:clear --quiet
php artisan cache:clear --quiet

# Crear enlace de storage si no existe
Write-Host "🔗 Verificando enlace de storage..." -ForegroundColor Yellow
if (-not (Test-Path "public/storage")) {
    php artisan storage:link --quiet
    Write-Host "✅ Enlace de storage creado" -ForegroundColor Green
}

# Crear directorio de documentos si no existe
Write-Host "📁 Verificando directorios de documentos..." -ForegroundColor Yellow
$storageDir = "storage/app/public/documentos_solicitante"
if (-not (Test-Path $storageDir)) {
    New-Item -ItemType Directory -Path $storageDir -Force | Out-Null
    Write-Host "✅ Directorio creado: $storageDir" -ForegroundColor Green
}

# Verificar archivo .env
if (-not (Test-Path ".env")) {
    Write-Host "⚠️  No se encontró archivo .env" -ForegroundColor Yellow
    Write-Host "💡 Copia .env.example a .env y configura tu base de datos" -ForegroundColor Cyan
}

# Mostrar información del servidor
Write-Host ""
Write-Host "✅ Configuración completada" -ForegroundColor Green
Write-Host "🌐 Servidor disponible en: http://127.0.0.1:$availablePort" -ForegroundColor Cyan
Write-Host "📁 Archivos se guardan en: storage/app/public/documentos_solicitante/" -ForegroundColor Cyan
Write-Host "⚠️  Para detener el servidor presiona Ctrl+C" -ForegroundColor Yellow
Write-Host ""

# Iniciar servidor en el puerto disponible
Write-Host "🚀 Iniciando servidor Laravel..." -ForegroundColor Green
php artisan serve --host=127.0.0.1 --port=$availablePort 