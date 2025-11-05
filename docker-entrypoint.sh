#!/bin/bash
set -e

# Wait for .env file if needed (can be mounted as volume)
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "APP_KEY=" > .env
    fi
fi

# Generate application key if not set
php artisan key:generate --ansi || true

# Clear caches before optimizing
php artisan config:clear || true
php artisan cache:clear || true

# Optimize Laravel for production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache
exec apache2-foreground

