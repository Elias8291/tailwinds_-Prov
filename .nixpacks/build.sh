#!/bin/bash
set -e

echo "Building Laravel application..."

# Laravel optimizations for production
php artisan config:cache
php artisan route:cache  
php artisan view:cache

echo "Build completed successfully!" 