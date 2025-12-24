<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaSimpanPinjamSeeder extends Seeder
{
    /**
     * COA Default untuk Koperasi Simpan Pinjam
     * Berdasarkan Pedoman Akuntansi Koperasi & SAK ETAP
     */
    public function run(): void
    {
        $akun = [
            // ===============================
            // 1. ASET (Asset)
            // ===============================
            // 1.1 Aset Lancar
            ['kode_akun' => '1-1100', 'nama_akun' => 'Kas', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1101', 'nama_akun' => 'Kas Kecil', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1200', 'nama_akun' => 'Bank BCA', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1201', 'nama_akun' => 'Bank Mandiri', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1202', 'nama_akun' => 'Bank BRI', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-1203', 'nama_akun' => 'Bank Syariah', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
            // Piutang Pinjaman Anggota
            ['kode_akun' => '1-2100', 'nama_akun' => 'Piutang Pinjaman Reguler', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2101', 'nama_akun' => 'Piutang Pinjaman Khusus', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2102', 'nama_akun' => 'Piutang Pinjaman Darurat', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2103', 'nama_akun' => 'Piutang Pinjaman Produktif', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2110', 'nama_akun' => 'Bunga Pinjaman YMH Diterima', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2200', 'nama_akun' => 'Piutang Lain-lain', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
            // PPAP (Penyisihan Penghapusan Aktiva Produktif)
            ['kode_akun' => '1-2900', 'nama_akun' => 'PPAP - Kolektibilitas Lancar', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2901', 'nama_akun' => 'PPAP - Dalam Perhatian Khusus', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2902', 'nama_akun' => 'PPAP - Kurang Lancar', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2903', 'nama_akun' => 'PPAP - Diragukan', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-2904', 'nama_akun' => 'PPAP - Macet', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Kredit', 'kategori' => 'Aset'],
            
            ['kode_akun' => '1-4100', 'nama_akun' => 'Biaya Dibayar Dimuka', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            ['kode_akun' => '1-4200', 'nama_akun' => 'Pendapatan YMH Diterima', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Aset'],
            
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
            // 2.1 Kewajiban kepada Anggota (Simpanan)
            ['kode_akun' => '2-1100', 'nama_akun' => 'Simpanan Pokok', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1101', 'nama_akun' => 'Simpanan Wajib', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1102', 'nama_akun' => 'Simpanan Sukarela', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1103', 'nama_akun' => 'Simpanan Berjangka', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1104', 'nama_akun' => 'Simpanan Khusus', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1110', 'nama_akun' => 'Bunga Simpanan YMH Dibayar', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            
            // 2.2 Kewajiban Lainnya
            ['kode_akun' => '2-1200', 'nama_akun' => 'Utang Usaha', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1300', 'nama_akun' => 'Utang Gaji', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1400', 'nama_akun' => 'Utang Pajak', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1500', 'nama_akun' => 'Dana Sosial YMH Disalurkan', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1600', 'nama_akun' => 'Dana Pendidikan YMH Disalurkan', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-1700', 'nama_akun' => 'Dana Pengembangan Koperasi', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            
            // 2.3 Liabilitas Jangka Panjang
            ['kode_akun' => '2-2100', 'nama_akun' => 'Utang Bank', 'tipe_akun' => 'Kewajiban Jangka Panjang', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],
            ['kode_akun' => '2-2200', 'nama_akun' => 'Pinjaman dari Induk Koperasi', 'tipe_akun' => 'Kewajiban Jangka Panjang', 'saldo_normal' => 'Kredit', 'kategori' => 'Kewajiban'],

            // ===============================
            // 3. EKUITAS (Modal)
            // ===============================
            ['kode_akun' => '3-1100', 'nama_akun' => 'Modal Sendiri', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1200', 'nama_akun' => 'Modal Penyertaan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1300', 'nama_akun' => 'Donasi / Hibah', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1400', 'nama_akun' => 'Cadangan Umum', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1500', 'nama_akun' => 'Cadangan Risiko', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1600', 'nama_akun' => 'SHU Tahun Berjalan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],
            ['kode_akun' => '3-1700', 'nama_akun' => 'SHU Tahun Lalu', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'kategori' => 'Ekuitas'],

            // ===============================
            // 4. PENDAPATAN
            // ===============================
            // Pendapatan Operasional Utama
            ['kode_akun' => '4-1100', 'nama_akun' => 'Pendapatan Bunga Pinjaman', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1101', 'nama_akun' => 'Pendapatan Bunga Pinjaman Reguler', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1102', 'nama_akun' => 'Pendapatan Bunga Pinjaman Khusus', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1200', 'nama_akun' => 'Pendapatan Provisi Pinjaman', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1300', 'nama_akun' => 'Pendapatan Administrasi Pinjaman', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-1400', 'nama_akun' => 'Pendapatan Denda Keterlambatan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            
            // Pendapatan Lainnya
            ['kode_akun' => '4-9100', 'nama_akun' => 'Pendapatan Bunga Bank', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],
            ['kode_akun' => '4-9200', 'nama_akun' => 'Pendapatan Lain-lain', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit', 'kategori' => 'Pendapatan'],

            // ===============================
            // 5. HPP (Beban Bunga Simpanan = Cost of Fund)
            // ===============================
            ['kode_akun' => '5-1100', 'nama_akun' => 'Beban Bunga Simpanan Pokok', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '5-1101', 'nama_akun' => 'Beban Bunga Simpanan Wajib', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '5-1102', 'nama_akun' => 'Beban Bunga Simpanan Sukarela', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '5-1103', 'nama_akun' => 'Beban Bunga Simpanan Berjangka', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],

            // ===============================
            // 6. BEBAN OPERASIONAL
            // ===============================
            ['kode_akun' => '6-1100', 'nama_akun' => 'Beban Gaji & Upah', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-1200', 'nama_akun' => 'Beban Tunjangan Pengurus', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-1300', 'nama_akun' => 'Beban BPJS', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2100', 'nama_akun' => 'Beban Sewa Kantor', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2200', 'nama_akun' => 'Beban Listrik & Air', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2300', 'nama_akun' => 'Beban Telepon & Internet', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-2400', 'nama_akun' => 'Beban ATK & Perlengkapan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3100', 'nama_akun' => 'Beban Penyusutan Bangunan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3200', 'nama_akun' => 'Beban Penyusutan Kendaraan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3300', 'nama_akun' => 'Beban Penyusutan Peralatan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-3400', 'nama_akun' => 'Beban Penyusutan Inventaris', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4100', 'nama_akun' => 'Beban RAT (Rapat Anggota Tahunan)', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4200', 'nama_akun' => 'Beban Rapat Pengurus', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4300', 'nama_akun' => 'Beban Pendidikan & Pelatihan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4400', 'nama_akun' => 'Beban Pemeliharaan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4500', 'nama_akun' => 'Beban Asuransi', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4600', 'nama_akun' => 'Beban Pajak', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4700', 'nama_akun' => 'Beban Administrasi Bank', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4800', 'nama_akun' => 'Beban PPAP', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-4900', 'nama_akun' => 'Beban Penghapusan Piutang', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
            ['kode_akun' => '6-9100', 'nama_akun' => 'Beban Bunga Bank', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit', 'kategori' => 'Beban'],
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

        $this->command->info('COA Simpan Pinjam (Savings & Loans) berhasil di-seed!');
    }
}
