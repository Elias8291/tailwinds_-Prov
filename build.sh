#!/bin/bash
set -e

echo "🔧 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Installing Node.js dependencies..."
npm ci --only=production

echo "🏗️ Building frontend assets..."
npm run build

echo "📁 Creating storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p database

echo "🗄️ Creating SQLite database..."
touch database/database.sqlite

echo "🔑 Generating application key..."
php artisan key:generate --force

echo "⚡ Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache

echo "🗃️ Running database migrations..."
php artisan migrate --force

echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✅ Build completed successfully!" 