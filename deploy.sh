#!/bin/bash
set -e

echo "🚀 Bismillah Memulai proses deployment..."

# 1. Masuk ke mode pemeliharaan
echo "🚧 Mengaktifkan mode pemeliharaan..."
php artisan down || true

# Pastikan aplikasi kembali UP jika script ini gagal (Error Handling)
trap 'echo "⚠️ Terjadi kesalahan! Mengaktifkan aplikasi kembali..."; php artisan up' ERR

# 1a. Pastikan izin akses folder benar (Permissions Fix)
echo "🔒 Mengatur ulang izin akses folder storage & cache..."
chmod -R 775 storage bootstrap/cache

# 2. Update kode dari repository
echo "📥 Menarik kode terbaru dari Git..."
git pull origin main

# 3. Instal dependensi PHP (Production mode)
echo "📦 Menginstal dependensi Composer..."
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 4. Migrasi Database Pusat
echo "🗄️ Menjalankan migrasi database pusat..."
php artisan migrate --force

# 5. Migrasi Database Tenant (Multi-database)
echo "🏢 Menjalankan migrasi database semua tenant..."
php artisan tenants:migrate --force

# 5a. Opsional: Jalankan Seeder khusus (Contoh: PermissionSeeder untuk Role Baru)
# Silakan hapus tanda '#' di baris bawah jika perlu menjalankan seeder setelah update
# php artisan tenants:seed --class=PermissionSeeder

# 6. Optimasasi Cache Laravel
echo "⚡ Mengoptimalkan cache sistem..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Build Frontend Assets (Vite)
if [ -f "package.json" ]; then
    echo "🎨 Membangun aset frontend (Vite)..."
#    npm install
#    npm run build
fi

# 8. Restart Queue Workers (untuk memastikan kode terbaru terbaca oleh background jobs)
echo "🔄 Merestart queue workers..."
php artisan queue:restart

# 9. Keluar dari mode pemeliharaan
echo "✅ Deployment selesai! Mengaktifkan aplikasi kembali..."
php artisan up

echo "🌟 Alhamdulilah Aplikasi sudah LIVE kembali dengan versi terbaru."
