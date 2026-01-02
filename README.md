# Simple Akunting v5

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?logo=bootstrap)

Sistem Akuntansi Terpadu berbasis Laravel dengan dukungan multi-bisnis: Dagang, Manufaktur, Pertanian (PSAK 69), dan Koperasi Simpan Pinjam.

## 🌟 Fitur Utama

### 📊 Akuntansi Dasar
- **Manajemen Akun (COA)** - Chart of Accounts dengan template standar per jenis usaha
- **Jurnal Umum** - Pencatatan jurnal dengan auto-posting
- **Buku Besar** - Laporan buku besar per akun
- **Neraca Saldo** - Trial Balance dengan periode yang dapat disesuaikan
- **Laporan Keuangan Lengkap**:
  - Neraca (Balance Sheet)
  - Laba Rugi (Income Statement)
  - Arus Kas (Cash Flow - Langsung & Tidak Langsung)
  - Perubahan Ekuitas
- **Tutup Buku** - Penutupan periode akuntansi

### 💰 Transaksi Bisnis
- **Penjualan** - Faktur penjualan dengan auto-jurnal
- **Pembelian** - Faktur pembelian dengan auto-jurnal
- **Penerimaan Pembayaran** - Pelunasan piutang
- **Pembayaran Utang** - Pelunasan utang usaha
- **Persediaan** - Manajemen stok dengan kartu stok
- **Kategori Persediaan**:
  - Barang Dagangan
  - Aset Biologis (PSAK 69)
  - Bahan Baku
  - Barang Dalam Proses (WIP)
  - Barang Jadi

### 🏭 Manufaktur
- **Bill of Materials (BOM)** - Struktur produk dan komponen
- **Order Produksi** - Perencanaan dan pelaksanaan produksi
- **Manajemen Bahan Baku** - Tracking penggunaan material
- **Barang Dalam Proses** - Work in Process monitoring
- **Barang Jadi** - Finished goods inventory
- **Biaya Produksi**:
  - Biaya Bahan Baku
  - Biaya Tenaga Kerja Langsung
  - Biaya Overhead Pabrik (BOP)

### 🌾 Pertanian (PSAK 69)
- **Aset Biologis** - Manajemen tanaman dan ternak
- **Penilaian Nilai Wajar** - Fair value measurement sesuai PSAK 69
- **Revaluasi Otomatis** - Pencatatan perubahan nilai wajar
- **Produk Agrikultur** - Hasil panen dan ternak
- **Jenis Aset Biologis**:
  - Tanaman Semusim (Annual Crops)
  - Hewan Ternak Potong
  - Tanaman Kehutanan
  - Hewan Ternak Bibit/Indukan
  - Tanaman Produktif (Bearer Plants)

### 🏦 Koperasi Simpan Pinjam
- **Manajemen Anggota** - Data anggota koperasi
- **Simpanan** - Simpanan Pokok, Wajib, Sukarela, Deposito
- **Pinjaman** - Permohonan dan pencairan pinjaman
- **Angsuran** - Pembayaran angsuran dengan jadwal
- **Approval Workflow** - Sistem persetujuan berjenjang
- **Laporan Koperasi**:
  - Outstanding Simpanan & Pinjaman
  - Kolektibilitas Pinjaman (sesuai OJK)
  - Perhitungan & Pembagian SHU

### 🏢 Multi-Cabang
- **Manajemen Cabang** - Kelola multiple lokasi bisnis
- **Data Terpisah** - Isolasi data per cabang
- **Konsolidasi** - Laporan gabungan semua cabang

## 🛠️ Teknologi

- **Backend**: Laravel 12.x
- **Frontend**: Bootstrap 5.3, Vite, Feather Icons
- **Database**: MySQL 8.0+
- **PHP**: 8.3+
- **Node.js**: 20.x+

## 📋 Persyaratan Sistem

- PHP >= 8.3
- MySQL >= 8.0 atau MariaDB >= 10.3
- Composer
- Node.js >= 20.x dan npm
- Extension PHP:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/solusigroup/simpleakunting-v5.git
cd simpleakunting-v5
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpleakunting
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database

```bash
php artisan migrate
```

### 5. Seed Data (Optional)

Seed COA standar berdasarkan jenis usaha:

```bash
php artisan db:seed --class=CoaTemplateSeeder
```

### 6. Build Assets

```bash
npm run build
```

Atau untuk development:

```bash
npm run dev
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## ⚙️ Konfigurasi

### Profil Perusahaan

Setelah instalasi, segera isi **Profil Perusahaan** di menu Admin:

1. Nama Perusahaan
2. **Jenis Usaha**: Pilih salah satu
   - Dagang
   - Manufaktur
   - Pertanian (PSAK 69)
   - Jasa
   - Multi Usaha
3. Alamat dan kontak
4. **Akun Default** untuk transaksi otomatis

### Chart of Accounts (COA)

Template COA akan dimuat otomatis berdasarkan jenis usaha:
- **Dagang**: 57 akun standar perdagangan
- **Manufaktur**: +25 akun produksi
- **Pertanian**: +19 akun agrikultur (PSAK 69)

Anda dapat menambah, edit, atau hapus akun sesuai kebutuhan.

### Tutup Buku

Disarankan untuk melakukan tutup buku setiap akhir periode:
1. Buka menu **Tutup Buku**
2. Pilih periode penutupan
3. Sistem akan otomatis:
   - Menutup akun laba rugi
   - Transfer ke Ikhtisar Laba Rugi
   - Update Laba Ditahan

## 📖 Panduan Penggunaan

### Alur Transaksi Dagang

1. **Setup Awal**:
   - Isi Profil Perusahaan
   - Cek/sesuaikan COA
   - Tambah data pelanggan & pemasok
   - Input persediaan awal

2. **Transaksi Pembelian**:
   - Buat Faktur Pembelian
   - Sistem auto-jurnal: Debit Persediaan, Kredit Utang
   - Bayar Utang saat jatuh tempo

3. **Transaksi Penjualan**:
   - Buat Faktur Penjualan
   - Sistem auto-jurnal: Debit Piutang, Kredit Penjualan
   - Auto-HPP: Debit HPP, Kredit Persediaan
   - Terima pembayaran

4. **Laporan**:
   - Cek Neraca & Laba Rugi
   - Analisis Arus Kas
   - Review Kartu Stok

### Alur Manufaktur

1. **Setup BOM**: Definisikan struktur produk
2. **Order Produksi**: Buat pesanan produksi
3. **Eksekusi Produksi**: 
   - Konsumsi Bahan Baku
   - Catat Tenaga Kerja
   - Alokasi Overhead
4. **Selesai Produksi**: Transfer ke Barang Jadi
5. **Penjualan**: Jual barang jadi

### Alur Koperasi

1. **Registrasi Anggota**: Input data anggota
2. **Simpanan**: Catat setoran simpanan
3. **Pinjaman**:
   - Anggota ajukan pinjaman
   - Approval oleh pengurus
   - Pencairan pinjaman
4. **Angsuran**: Catat pembayaran angsuran
5. **Laporan**: 
   - Outstanding
   - Kolektibilitas
   - Perhitungan SHU

## 🗂️ Struktur Folder

```
simpleakunting-v5/
│
├── app/
│   ├── Http/Controllers/        # Controllers
│   ├── Models/                  # Eloquent Models
│   └── ...
│
├── database/
│   ├── migrations/              # Database Migrations
│   └── seeders/                 # Data Seeders
│       └── CoaTemplateSeeder.php
│
├── resources/
│   └── views/                   # Blade Templates
│       ├── accounting/          # Tutup Buku
│       ├── agriculture/         # PSAK 69
│       ├── cabang/              # Multi-cabang
│       ├── laporan/             # Reports
│       ├── manufacturing/       # Manufaktur
│       └── ...
│
├── routes/
│   └── web.php                  # Web Routes
│
└── public/                      # Public Assets
```

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## 📝 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

## 📧 Kontak

- **Developer**: Solusi Group
- **Email**: kurniawan@petalmail.com
- **GitHub**: [@solusigroup](https://github.com/solusigroup)

## 🙏 Acknowledgments

- Laravel Framework
- Bootstrap
- Feather Icons
- Seluruh kontributor open source

---

**Simple Akunting v5** - Sistem Akuntansi Terpadu untuk UMKM Indonesia 🇮🇩
