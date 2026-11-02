#!/bin/bash
# ==========================================================
#  deploy.sh - Script Deploy Otomatis untuk Aplikasi Penilaian
#  Penggunaan: bash deploy.sh
# ==========================================================

set -e  # Berhenti jika ada error

APP_DIR=~/apps/aplikasi_penilaian
CONTAINER_NAME=aplikasi-penilaian-franken
WORKER_NAME=aplikasi-penilaian-worker
BRANCH=master

echo ""
echo "=========================================="
echo "  🚀 Memulai Deploy Aplikasi Penilaian..."
echo "=========================================="
echo ""

# 1. Masuk ke direktori aplikasi
cd "$APP_DIR"
echo "📂 Direktori: $(pwd)"

# 2. Fetch & Pull dari GitHub
echo ""
echo "📥 [1/10] Mengambil kode terbaru dari GitHub..."
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
echo "   ✅ Kode terbaru berhasil ditarik."

# 3. Build ulang Docker image jika Dockerfile / Caddyfile berubah
echo ""
echo "🔨 [2/10] Rebuild Docker image..."
docker compose build
echo "   ✅ Image berhasil di-build."

# 4. Restart container utama
echo ""
echo "🔄 [3/10] Restart container (web + worker)..."
docker compose down
docker compose up -d
echo "   ✅ Container web dan worker berhasil dinyalakan."

# 5. Update dependensi Composer dari dalam container
echo ""
echo "📦 [4/10] Install PHP dependencies (Composer)..."
docker exec "$CONTAINER_NAME" composer install --no-dev --optimize-autoloader
echo "   ✅ Composer dependencies siap."

# 6. Build frontend assets (Vite)
echo ""
echo "🎨 [5/10] Build frontend assets (Vite)..."
docker run --rm -v "$(pwd):/app" -w /app node:20-alpine sh -c "npm install && npm run build && chmod -R 755 public/build"
echo "   ✅ Assets berhasil di-build dengan permission 755."

# 7. Jalankan migrasi & reset cache permission
echo ""
echo "🗄️  [6/10] Menjalankan migrasi database..."
docker exec "$CONTAINER_NAME" php artisan migrate --force
docker exec "$CONTAINER_NAME" php artisan permission:cache-reset
echo "   ✅ Migrasi selesai."

# 8. Pastikan storage link dan permissions
echo ""
echo "🔗 [7/10] Verifikasi storage link dan permissions..."
docker exec "$CONTAINER_NAME" php artisan storage:link || true
docker exec "$CONTAINER_NAME" chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo "   ✅ Permissions terverifikasi."

# 9. Optimasi Laravel (cache config, route, view, event)
echo ""
echo "⚡ [8/10] Optimasi Laravel..."
docker exec "$CONTAINER_NAME" php artisan optimize:clear
docker exec "$CONTAINER_NAME" php artisan config:cache
docker exec "$CONTAINER_NAME" php artisan route:cache
docker exec "$CONTAINER_NAME" php artisan view:cache
docker exec "$CONTAINER_NAME" php artisan event:cache
echo "   ✅ Cache berhasil di-generate."

# 10. Restart queue worker
echo ""
echo "👷 [9/10] Restart Queue Worker..."
docker restart "$WORKER_NAME"
echo "   ✅ Worker di-restart dengan kode terbaru."

# 11. Bersihkan image Docker yang tidak terpakai
echo ""
echo "🧹 [10/10] Membersihkan image Docker lama yang tidak terpakai..."
docker image prune -f
echo "   ✅ Image lama dibersihkan."

# 12. Verifikasi
echo ""
echo "=========================================="
echo "  ✅ Deploy Selesai!"
echo "=========================================="
echo ""
echo "📊 Status Container:"
docker ps --filter "name=aplikasi-penilaian" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""
