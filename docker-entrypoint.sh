#!/bin/bash
set -e

# Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Build Vite assets jika belum ada
if [ ! -f /app/public/build/manifest.json ]; then
    echo "Building Vite assets..."
    npm install
    npm run build
    rm -rf node_modules
fi

# Set permission Laravel
chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Jalankan FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
