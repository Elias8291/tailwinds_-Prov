#!/bin/bash

# Railway Start Script for Laravel
echo "🚀 Starting Laravel application..."

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations if needed
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
fi

# Clear and cache config for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application
echo "✅ Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000} 