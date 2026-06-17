#!/bin/bash
set -e

echo "Starting deployment..."

# Masuk mode maintenance
php artisan down || true

# Ambil perubahan terbaru dari git
git pull origin main

# Install dependensi PHP
composer install --no-interaction --prefer-dist --optimize-autoloader

# Bersihkan cache Laravel
php artisan optimize:clear

# Jalankan migrasi database
php artisan migrate --force

# Install dependensi Node.js dan build aset (jika menggunakan Vite/Mix)
npm install
npm run build

# Keluar dari mode maintenance
php artisan up

echo "Deployment finished!"
