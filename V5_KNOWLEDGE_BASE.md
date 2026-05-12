# SimpleAkunting v5 - Knowledge Base

Dokumen ini mencatat informasi krusial untuk **SimpleAkunting v5** agar tidak tertukar dengan versi sebelumnya (v4).

## 🌍 Environment & Lokasi

| Item | Detail (v5) | Perbedaan dengan v4 |
|------|-------------|---------------------|
| **Server** | Mentari VPS (172.16.30.204) | Berbeda VPS |
| **Path** | `/var/www/simpleakunting/` | v4 ada di `/home/simpleak/` |
| **Domain** | `simpleakunting.id` | v4 menggunakan domain lain |
| **Laravel** | Version 12.x | v4 menggunakan versi lebih lama |
| **PHP** | PHP 8.3 | v4 mungkin menggunakan PHP 8.1/8.2 |

---

## 🏗️ Arsitektur Multi-Tenant

Sistem menggunakan **Stancl Tenancy**.

- **Central Database**: `dbv5_central`
- **Tenant Database**: `dbv5_{tenant_id}` (Contoh: `dbv5_demojaya`)
- **Domain Pattern**: `{tenant}.simpleakunting.id`

---

## 🛠️ Konfigurasi Penting (Lessons Learned)

### 1. SSL & Cloudflare (Flexible Mode)
Karena VPS hanya mendengarkan di Port 80, kita menggunakan **Cloudflare Flexible SSL**. Agar tidak terjadi `ERR_FAILED` dan redirect loop:
- **Wajib** memaksakan HTTPS di `AppServiceProvider.php` menggunakan `URL::forceScheme('https')`.
- **Wajib** mengatur `trustProxies(at: '*')` di `bootstrap/app.php`.

### 2. Session Driver
- Driver yang digunakan: `file`.
- Pastikan folder `storage/framework/sessions` memiliki izin akses `775` dan grup `www-data`.

### 3. Tom Select (Searchable Dropdowns)
Untuk menghindari dropdown terpotong oleh `overflow: hidden` pada container tabel atau modal:
- Gunakan opsi `dropdownParent: 'body'`.
- Inisialisasi ulang jika baris tabel ditambahkan secara dinamis (AJAX).

### 4. Diagnosa Neraca (Audit Tool)
- Logika audit dikonsolidasikan dalam `LaporanController` untuk konsistensi lingkungan.
- Query menggunakan subquery yang dioptimalkan untuk menghindari `Timeout` pada dataset besar.
- Mengandalkan validasi `is_approved = 1` pada modul Ritase untuk akurasi data.

### 5. PWA (Progressive Web App)
- File `manifest.json` dan `sw.js` berada di direktori `public/`.
- Pastikan logo berukuran minimal 192x192 dan 512x512 tersedia untuk memenuhi syarat instalasi browser.

---

## 🚀 Perintah Operasional (Cheatsheet)

### Deployment & Maintenance
```bash
cd /var/www/simpleakunting
./deploy.sh                      # Jalankan script deploy otomatis
php artisan tenants:migrate      # Migrate database semua tenant
php artisan config:clear         # Clear cache setelah ganti .env
```

### Database Access
```bash
sudo mysql -e "SHOW DATABASES LIKE 'dbv5%';"
sudo mysql -e "USE dbv5_central; SELECT * FROM domains;"
```

---

## 📝 Troubleshooting History

| Masalah | Solusi |
|---------|--------|
| `ERR_FAILED` saat login | Pastikan `URL::forceScheme('https')` aktif jika menggunakan Cloudflare Flexible. |
| Session File Permission | `sudo chown -R www-data:www-data storage && sudo chmod -R 775 storage` |
| Database Unknown | Pastikan menggunakan prefix `dbv5_` untuk v5. |
| Audit Tool 500 Error | Gunakan `LaporanController` yang telah dikonsolidasikan & optimasi query database. |
| Dropdown Terpotong | Tambahkan `dropdownParent: 'body'` pada konfigurasi Tom Select. |
| PWA Tak Muncul | Pastikan Service Worker terdaftar di `login.blade.php` atau `app.blade.php`. |

---
*Terakhir diperbarui: 12 Mei 2026*
