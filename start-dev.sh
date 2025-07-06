#!/bin/bash

# Script de inicio para desarrollo local
echo "🚀 Iniciando aplicación Laravel en modo desarrollo..."

# Verificar si existe la configuración PHP personalizada
if [ ! -f "php-dev.ini" ]; then
    echo "❌ No se encontró php-dev.ini"
    exit 1
fi

# Limpiar cache de desarrollo
echo "🧹 Limpiando cache de desarrollo..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Iniciar servidor con configuración PHP personalizada
echo "✅ Iniciando servidor con configuración PHP optimizada para archivos grandes..."
echo "📁 Límites: upload_max_filesize=50M, post_max_size=60M"
echo "🌐 Servidor disponible en: http://127.0.0.1:8000"
echo "⚠️  Para detener el servidor presiona Ctrl+C"
echo ""

php -c php-dev.ini artisan serve --port=8000 