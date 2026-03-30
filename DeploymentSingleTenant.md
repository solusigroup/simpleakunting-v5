🛑 Status Saat Ini: Mode Trial (Single-Tenant)
Aplikasi saat ini di-deploy sebagai versi trial/live demo pada lingkungan shared hosting.

Hosting: Shared Hosting di arenhost.id
Domain: abyaktalkd.simpleakunting.id
Mode: Single-Tenant (TENANCY_ENABLED=false)
Database: simpleak_abyaktalkdv5 (via MariaDB/MySQL bawaan cPanel)
Setup:
Menggunakan konfigurasi 
.env
 khusus shared hosting.
Queue di-set ke sync karena limitasi background process di shared hosting.
File asset (Vite) di-build lokal lalu di-upload (folder public/build).
Semua route transaksi dan pengaturan berada di routes/web.php (sudah disesuaikan agar berjalan normal tanpa multi-tenant).
🛠️ Perubahan & Perbaikan Keamanan yang Telah Dilakukan
Berdasarkan audit komprehensif hari ini, kode utama (di GitHub main branch) sudah diperbaiki dan sangat aman:

Keamanan Route (Central & Tenant):
Route pendaftaran tenant & admin panel dilindungi middleware auth dan role admin/superuser.
Fitur registrasi publik (self-registration) dimatikan.
Route login diberikan batasan percobaan (throttle:5,1).
Seluruh route transaksi & koperasi diwajibkan memiliki role spesifik (superuser, admin, manajer, staff).
Keamanan Database:
Fitur hapus/reset database (DatabaseController) diblokir sepenuhnya di environment production.
Pengeksekusian Seeder (via UI) dibatasi dengan whitelist yang ketat.
Konfigurasi Lokal:
Timezone diatur ke Asia/Jakarta.
Locale (bahasa utama) diubah menjadi id (Bahasa Indonesia).
Penyesuaian Single-Tenant:
Menambahkan route vital (cabang.switch, unit-usaha, dll) ke routes/web.php yang sebelumnya hanya ada di tenant.php, sehingga dashboard bisa diakses dengan baik di mode non-tenancy.
🚀 Rencana Masa Depan: Migrasi Multi-Tenant (VPS Mentari)
Ketika masa trial selesai dan aplikasi siap melayani banyak tenant/perusahaan secara terpisah, kita akan mengembalikannya ke mode aslinya:

Target Server: VPS Mentari COMMSINDO
IP Publik: 103.153.3.177
IP Private: 172.16.30.204
Domain: simpleakunting.id (Central) dan *.simpleakunting.id (Wildcard untuk Tenant)
Konfigurasi Utama:
TENANCY_ENABLED=true
Setup DNS Wildcard A Record.
Setup SSL Wildcard Let's Encrypt.
Konfigurasi MySQL User dengan hak akses GRANT OPTION (agar bisa auto-create database setiap ada pendaftaran tenant baru).
Menjalankan Queue Worker menggunakan Supervisor di VPS.
Langkah Persiapan Migrasi (Nanti):
Siapkan VPS (Install Nginx, PHP 8.2+, MySQL 8+).
Clone repositori terbaru dari GitHub.
Sesuaikan .env untuk production multi-tenant (menggunakan contoh dari .env.example yang sudah kita perbarui hari ini).
Deploy, konfigurasi domain, dan buat user admin utama via create_admin.php.

Comment
Ctrl+Alt+M
