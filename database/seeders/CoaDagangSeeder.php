<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaDagangSeeder extends Seeder
{
    /**
     * COA Default untuk Usaha Dagang
     * Standar Akuntansi Indonesia (SAK EMKM/PSAK)
     */
    public function run(): void
    {
        $akun = [
            // ===============================
            // 1. ASET (Asset)
            // ===============================
            // 1.1 Aset Lancar
            ['kode_akun' => '1-1100', 'nama_akun' => 'Kas', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1101', 'nama_akun' => 'Kas Kecil (Petty Cash)', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1200', 'nama_akun' => 'Bank BCA', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1201', 'nama_akun' => 'Bank Mandiri', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1202', 'nama_akun' => 'Bank BRI', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
            ['kode_akun' => '1-2100', 'nama_akun' => 'Piutang Usaha', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2200', 'nama_akun' => 'Piutang Karyawan', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2900', 'nama_akun' => 'Cadangan Kerugian Piutang', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            
            ['kode_akun' => '1-3100', 'nama_akun' => 'Persediaan Barang Dagang', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-3200', 'nama_akun' => 'Persediaan Bahan Baku', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
            ['kode_akun' => '1-4100', 'nama_akun' => 'Uang Muka Pembelian', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-4200', 'nama_akun' => 'Biaya Dibayar Dimuka', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-4300', 'nama_akun' => 'PPN Masukan', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
            // 1.2 Aset Tetap
            ['kode_akun' => '1-5100', 'nama_akun' => 'Tanah', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5200', 'nama_akun' => 'Bangunan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5201', 'nama_akun' => 'Akumulasi Penyusutan Bangunan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5300', 'nama_akun' => 'Kendaraan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5301', 'nama_akun' => 'Akumulasi Penyusutan Kendaraan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5400', 'nama_akun' => 'Peralatan Kantor', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5401', 'nama_akun' => 'Akumulasi Penyusutan Peralatan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5500', 'nama_akun' => 'Inventaris', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-5501', 'nama_akun' => 'Akumulasi Penyusutan Inventaris', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],

            // ===============================
            // 2. LIABILITAS (Kewajiban)
            // ===============================
            // 2.1 Liabilitas Jangka Pendek
            ['kode_akun' => '2-1100', 'nama_akun' => 'Utang Usaha', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1200', 'nama_akun' => 'Utang Bank Jangka Pendek', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1300', 'nama_akun' => 'Utang Gaji', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1400', 'nama_akun' => 'Utang Pajak', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1500', 'nama_akun' => 'PPN Keluaran', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1600', 'nama_akun' => 'Pendapatan Diterima Dimuka', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            
            // 2.2 Liabilitas Jangka Panjang
            ['kode_akun' => '2-2100', 'nama_akun' => 'Utang Bank Jangka Panjang', 'tipe_akun' => 'Kewajiban Jangka Panjang', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-2200', 'nama_akun' => 'Utang Leasing', 'tipe_akun' => 'Kewajiban Jangka Panjang', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],

            // ===============================
            // 3. EKUITAS (Modal)
            // ===============================
            ['kode_akun' => '3-1100', 'nama_akun' => 'Modal Disetor', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1200', 'nama_akun' => 'Laba Ditahan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1300', 'nama_akun' => 'Laba Tahun Berjalan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1400', 'nama_akun' => 'Prive', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Debit', 'kategori' => 'Ekuitas'],

            // ===============================
            // 4. PENDAPATAN
            // ===============================
            ['kode_akun' => '4-1100', 'nama_akun' => 'Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1200', 'nama_akun' => 'Diskon Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1300', 'nama_akun' => 'Retur Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-2100', 'nama_akun' => 'Pendapatan Jasa', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-9100', 'nama_akun' => 'Pendapatan Bunga', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-9200', 'nama_akun' => 'Pendapatan Lain-lain', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],

            // ===============================
            // 5. HARGA POKOK PENJUALAN (HPP)
            // ===============================
            ['kode_akun' => '5-1100', 'nama_akun' => 'Harga Pokok Penjualan', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'HPP'],
            ['kode_akun' => '5-1200', 'nama_akun' => 'Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'HPP'],
            ['kode_akun' => '5-1300', 'nama_akun' => 'Diskon Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Kredit', 'kategori' => 'HPP'],
            ['kode_akun' => '5-1400', 'nama_akun' => 'Retur Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Kredit', 'kategori' => 'HPP'],
            ['kode_akun' => '5-1500', 'nama_akun' => 'Ongkos Kirim Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'HPP'],

            // ===============================
            // 6. BEBAN OPERASIONAL
            // ===============================
            ['kode_akun' => '6-1100', 'nama_akun' => 'Beban Gaji & Upah', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-1200', 'nama_akun' => 'Beban Tunjangan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-1300', 'nama_akun' => 'Beban BPJS', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2100', 'nama_akun' => 'Beban Sewa', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2200', 'nama_akun' => 'Beban Listrik & Air', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2300', 'nama_akun' => 'Beban Telepon & Internet', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2400', 'nama_akun' => 'Beban ATK & Perlengkapan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2500', 'nama_akun' => 'Beban Ongkos Kirim', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3100', 'nama_akun' => 'Beban Penyusutan Bangunan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3200', 'nama_akun' => 'Beban Penyusutan Kendaraan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3300', 'nama_akun' => 'Beban Penyusutan Peralatan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3400', 'nama_akun' => 'Beban Penyusutan Inventaris', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4100', 'nama_akun' => 'Beban Iklan & Promosi', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4200', 'nama_akun' => 'Beban Transportasi', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4300', 'nama_akun' => 'Beban Entertaining', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4400', 'nama_akun' => 'Beban Pemeliharaan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4500', 'nama_akun' => 'Beban Asuransi', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4600', 'nama_akun' => 'Beban Pajak', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4700', 'nama_akun' => 'Beban Administrasi Bank', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4800', 'nama_akun' => 'Beban Kerugian Piutang', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-9100', 'nama_akun' => 'Beban Bunga', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-9200', 'nama_akun' => 'Beban Lain-lain', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
        ];

        foreach ($akun as $a) {
            DB::table('akun')->updateOrInsert(
                ['kode_akun' => $a['kode_akun']],
                array_merge($a, [
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('COA Dagang (Trading) berhasil di-seed!');
    }
}
