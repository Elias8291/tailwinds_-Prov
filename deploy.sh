#!/bin/bash
set -e

echo "🚀 Starting post-deployment tasks..."

echo "🔑 Generating application key if needed..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

echo "🗃️ Running database migrations..."
php artisan migrate --force

echo "🌱 Seeding database..."
php artisan db:seed --force

echo "⚡ Clearing and caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔗 Creating storage symlink..."
php artisan storage:link

echo "✅ Deployment tasks completed successfully!" 