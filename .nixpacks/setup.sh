#!/bin/bash
echo "Setting up Laravel application..."

# Create required directories
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache 