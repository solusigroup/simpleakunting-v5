-- Database Schema for Simple Akunting v2
-- Generated based on Laravel Migrations

SET FOREIGN_KEY_CHECKS=0;

-- 1. Tabel Users (Legacy Structure)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_nama_user_unique` (`nama_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Akun
DROP TABLE IF EXISTS `akun`;
CREATE TABLE `akun` (
  `kode_akun` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_akun` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_akun` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo_normal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Perusahaan
DROP TABLE IF EXISTS `perusahaan`;
CREATE TABLE `perusahaan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_piutang_default` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_utang_default` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_direktur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_akuntan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Pelanggan
DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE `pelanggan` (
  `id_pelanggan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_awal_piutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo_terkini_piutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Pemasok
DROP TABLE IF EXISTS `pemasok`;
CREATE TABLE `pemasok` (
  `id_pemasok` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_pemasok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_awal_hutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo_terkini_hutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemasok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Master Persediaan
DROP TABLE IF EXISTS `master_persediaan`;
CREATE TABLE `master_persediaan` (
  `id_barang` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok_awal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stok_saat_ini` decimal(10,2) NOT NULL DEFAULT '0.00',
  `harga_beli` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_jual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `akun_persediaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_hpp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_penjualan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `master_persediaan_kode_barang_unique` (`kode_barang`),
  UNIQUE KEY `master_persediaan_barcode_unique` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel Jurnal Umum
DROP TABLE IF EXISTS `jurnal_umum`;
CREATE TABLE `jurnal_umum` (
  `id_jurnal` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `sumber_jurnal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Jurnal Detail
DROP TABLE IF EXISTS `jurnal_detail`;
CREATE TABLE `jurnal_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_jurnal` bigint(20) unsigned NOT NULL,
  `kode_akun` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Penjualan
DROP TABLE IF EXISTS `penjualan`;
CREATE TABLE `penjualan` (
  `id_penjualan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned DEFAULT NULL,
  `no_faktur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `akun_kas_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_penjualan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabel Penjualan Detail
DROP TABLE IF EXISTS `penjualan_detail`;
CREATE TABLE `penjualan_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` bigint(20) unsigned NOT NULL,
  `id_barang` bigint(20) unsigned NOT NULL,
  `kuantitas` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `akun_pendapatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabel Pembelian
DROP TABLE IF EXISTS `pembelian`;
CREATE TABLE `pembelian` (
  `id_pembelian` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pemasok` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned DEFAULT NULL,
  `no_faktur_pembelian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `akun_kas_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembelian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tabel Pembelian Detail
DROP TABLE IF EXISTS `pembelian_detail`;
CREATE TABLE `pembelian_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembelian` bigint(20) unsigned NOT NULL,
  `id_barang` bigint(20) unsigned NOT NULL,
  `kuantitas` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `akun_beban_persediaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tabel Kartu Stok
DROP TABLE IF EXISTS `kartu_stok`;
CREATE TABLE `kartu_stok` (
  `id_kartu` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_barang` bigint(20) unsigned NOT NULL,
  `tipe_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuantitas` decimal(10,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kartu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Laravel Standard Tables
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA

-- Akun (Chart of Accounts)
INSERT INTO `akun` (`kode_akun`, `nama_akun`, `tipe_akun`, `saldo_normal`, `created_at`, `updated_at`) VALUES
('1-10001', 'Kas Kecil', 'Kas & Bank', 'Debit', NOW(), NOW()),
('1-10002', 'Bank BCA', 'Kas & Bank', 'Debit', NOW(), NOW()),
('1-10003', 'Bank Mandiri', 'Kas & Bank', 'Debit', NOW(), NOW()),
('1-10100', 'Piutang Usaha', 'Piutang', 'Debit', NOW(), NOW()),
('1-10200', 'Persediaan Barang Dagang', 'Persediaan', 'Debit', NOW(), NOW()),
('1-10300', 'Perlengkapan', 'Aset Lancar Lainnya', 'Debit', NOW(), NOW()),
('1-10400', 'Sewa Dibayar Dimuka', 'Aset Lancar Lainnya', 'Debit', NOW(), NOW()),
('1-20100', 'Peralatan Kantor', 'Aset Tetap', 'Debit', NOW(), NOW()),
('1-20101', 'Akum. Peny. Peralatan', 'Aset Tetap', 'Kredit', NOW(), NOW()),
('1-20200', 'Kendaraan', 'Aset Tetap', 'Debit', NOW(), NOW()),
('1-20201', 'Akum. Peny. Kendaraan', 'Aset Tetap', 'Kredit', NOW(), NOW()),
('2-10100', 'Utang Usaha', 'Utang Usaha', 'Kredit', NOW(), NOW()),
('2-10200', 'Utang Gaji', 'Kewajiban Lancar Lainnya', 'Kredit', NOW(), NOW()),
('2-10300', 'Utang Pajak', 'Kewajiban Lancar Lainnya', 'Kredit', NOW(), NOW()),
('2-20100', 'Utang Bank Jangka Panjang', 'Kewajiban Jangka Panjang', 'Kredit', NOW(), NOW()),
('3-10000', 'Modal Pemilik', 'Ekuitas', 'Kredit', NOW(), NOW()),
('3-20000', 'Prive Pemilik', 'Ekuitas', 'Debit', NOW(), NOW()),
('3-30000', 'Laba Ditahan', 'Ekuitas', 'Kredit', NOW(), NOW()),
('4-10000', 'Penjualan Barang', 'Pendapatan', 'Kredit', NOW(), NOW()),
('4-20000', 'Pendapatan Jasa', 'Pendapatan', 'Kredit', NOW(), NOW()),
('4-30000', 'Retur Penjualan', 'Pendapatan', 'Debit', NOW(), NOW()),
('4-40000', 'Potongan Penjualan', 'Pendapatan', 'Debit', NOW(), NOW()),
('5-10000', 'Harga Pokok Penjualan', 'HPP', 'Debit', NOW(), NOW()),
('6-10001', 'Beban Gaji', 'Beban', 'Debit', NOW(), NOW()),
('6-10002', 'Beban Sewa', 'Beban', 'Debit', NOW(), NOW()),
('6-10003', 'Beban Listrik, Air & Telp', 'Beban', 'Debit', NOW(), NOW()),
('6-10004', 'Beban Perlengkapan', 'Beban', 'Debit', NOW(), NOW()),
('6-10005', 'Beban Penyusutan', 'Beban', 'Debit', NOW(), NOW()),
('6-10006', 'Beban Pemasaran', 'Beban', 'Debit', NOW(), NOW()),
('6-10007', 'Beban Lain-lain', 'Beban', 'Debit', NOW(), NOW()),
('8-10000', 'Pendapatan Bunga', 'Pendapatan Lainnya', 'Kredit', NOW(), NOW()),
('9-10000', 'Beban Administrasi Bank', 'Beban Lainnya', 'Debit', NOW(), NOW());

-- Perusahaan (Company Profile)
INSERT INTO `perusahaan` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `email`, `akun_piutang_default`, `akun_utang_default`, `created_at`, `updated_at`) VALUES
(1, 'PT. Contoh Perusahaan', 'Jl. Contoh No. 123, Jakarta', '021-12345678', 'info@contoh.com', '1-10100', '2-10100', NOW(), NOW());

SET FOREIGN_KEY_CHECKS=1;
