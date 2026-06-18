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
echo "📥 [1/9] Mengambil kode terbaru dari GitHub..."
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
echo "   ✅ Kode terbaru berhasil ditarik."

# 3. Build ulang Docker image
echo ""
echo "🔨 [2/9] Rebuild Docker image..."
docker compose build
echo "   ✅ Image berhasil di-build."

# 4. Build frontend assets (Vite)
echo ""
echo "📦 [3/9] Build frontend assets..."
docker run --rm -v "$(pwd):/app" -w /app node:20-alpine sh -c "npm install && npm run build && chown -R $(id -u):$(id -g) public/build node_modules"
echo "   ✅ Assets berhasil di-build."

# 5. Restart semua container (web + worker)
echo ""
echo "🔄 [4/9] Restart container (web + worker)..."
docker compose down
docker compose up -d
echo "   ✅ Container web dan worker berhasil dinyalakan."

# 6. Jalankan migrasi (tanpa --fresh, hanya yang baru)
echo ""
echo "🗄️  [5/9] Menjalankan migrasi database..."
docker exec "$CONTAINER_NAME" php artisan migrate --force
docker exec "$CONTAINER_NAME" php artisan permission:cache-reset
docker exec "$CONTAINER_NAME" php artisan shield:generate --all --ignore-existing-policies --option=policies_and_permissions --no-interaction
echo "   ✅ Migrasi dan Shield selesai."

# 7. Optimasi Laravel (cache config, route, view)
echo ""
echo "⚡ [6/9] Optimasi Laravel..."
docker exec "$CONTAINER_NAME" php artisan config:cache
docker exec "$CONTAINER_NAME" php artisan route:cache
docker exec "$CONTAINER_NAME" php artisan view:cache
docker exec "$CONTAINER_NAME" php artisan event:cache
echo "   ✅ Cache berhasil di-generate."

# 8. Restart queue worker
echo ""
echo "👷 [7/9] Restart Queue Worker..."
docker restart "$WORKER_NAME"
echo "   ✅ Worker di-restart dengan kode terbaru."

# 9. Bersihkan image Docker yang tidak terpakai
echo ""
echo "🧹 [8/9] Membersihkan image Docker lama yang tidak terpakai..."
docker image prune -f
echo "   ✅ Image lama dibersihkan."

# 10. Verifikasi
echo ""
echo "=========================================="
echo "  ✅ Deploy Selesai!"
echo "=========================================="
echo ""
echo "📊 Status Container:"
docker ps --filter "name=aplikasi-penilaian" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""
echo "🧠 PHP Memory Limit:"
docker exec "$CONTAINER_NAME" php -r "echo ini_get('memory_limit') . PHP_EOL;"
echo ""
echo "⏱️  Max Execution Time:"
docker exec "$CONTAINER_NAME" php -r "echo ini_get('max_execution_time') . PHP_EOL;"
echo ""
