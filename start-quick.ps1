# Script de inicio rápido para Laravel
Write-Host "🚀 Inicio rápido de Laravel..." -ForegroundColor Green

# Verificar PHP
if (-not (Get-Command "php" -ErrorAction SilentlyContinue)) {
    Write-Host "❌ PHP no encontrado" -ForegroundColor Red
    exit 1
}

# Verificar artisan
if (-not (Test-Path "artisan")) {
    Write-Host "❌ No estás en el directorio de Laravel" -ForegroundColor Red
    exit 1
}

# Limpiar cache básico
Write-Host "🧹 Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear --quiet
php artisan route:clear --quiet

# Crear storage link si no existe
if (-not (Test-Path "public/storage")) {
    php artisan storage:link --quiet
}

# Buscar puerto disponible
$port = 8000
while ($port -le 8010) {
    try {
        $listener = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Parse("127.0.0.1"), $port)
        $listener.Start()
        $listener.Stop()
        break
    }
    catch {
        $port++
    }
}

if ($port -gt 8010) {
    Write-Host "❌ No hay puertos disponibles" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Iniciando en puerto $port" -ForegroundColor Green
Write-Host "🌐 http://127.0.0.1:$port" -ForegroundColor Cyan
Write-Host ""

php artisan serve --host=127.0.0.1 --port=$port 