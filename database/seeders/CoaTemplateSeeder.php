<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the company's business type
        $jenisUsaha = DB::table('perusahaan')->where('id', 1)->value('jenis_usaha') ?? 'dagang';
        
        // Clear existing COA if requested
        // DB::table('akun')->truncate();
        
        // Insert base COA (Dagang - always included)
        $this->seedDagangCoa();
        
        // Add Manufaktur accounts if needed
        if (in_array($jenisUsaha, ['manufaktur', 'multi'])) {
            $this->seedManufakturCoa();
        }
        
        // Add PSAK 69 (Agriculture) accounts if needed
        if (in_array($jenisUsaha, ['pertanian', 'multi'])) {
            $this->seedPsak69Coa();
        }
    }

    /**
     * Seed base COA for Trading (Dagang) companies
     */
    private function seedDagangCoa(): void
    {
        $accounts = [
            // ASET LANCAR
            ['kode_akun' => '1-1000', 'nama_akun' => 'Aset Lancar', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1100', 'nama_akun' => 'Kas Kecil', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1200', 'nama_akun' => 'Bank Mandiri/BCA', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1300', 'nama_akun' => 'Piutang Usaha', 'tipe_akun' => 'Piutang Usaha', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1310', 'nama_akun' => 'Cadangan Kerugian Piutang', 'tipe_akun' => 'Piutang Usaha', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '1-1400', 'nama_akun' => 'Persediaan Barang Dagang', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1500', 'nama_akun' => 'Perlengkapan Kantor', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1600', 'nama_akun' => 'Sewa Dibayar Dimuka', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1700', 'nama_akun' => 'PPN Masukan', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1800', 'nama_akun' => 'Uang Muka Pembelian', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],
            
            // ASET TETAP
            ['kode_akun' => '1-2000', 'nama_akun' => 'Aset Tetap', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2100', 'nama_akun' => 'Tanah', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2200', 'nama_akun' => 'Bangunan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2300', 'nama_akun' => 'Kendaraan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2310', 'nama_akun' => 'Akumulasi Penyusutan Kendaraan', 'tipe_akun' => 'Akumulasi Penyusutan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '1-2400', 'nama_akun' => 'Peralatan Kantor', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2410', 'nama_akun' => 'Akumulasi Penyusutan Peralatan', 'tipe_akun' => 'Akumulasi Penyusutan', 'saldo_normal' => 'Kredit'],
            
            // LIABILITAS JANGKA PENDEK
            ['kode_akun' => '2-1000', 'nama_akun' => 'Liabilitas Jangka Pendek', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-1100', 'nama_akun' => 'Utang Usaha', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-1200', 'nama_akun' => 'Utang Gaji', 'tipe_akun' => 'Liabilitas Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-1300', 'nama_akun' => 'PPN Keluaran', 'tipe_akun' => 'Liabilitas Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-1400', 'nama_akun' => 'Utang PPh 21/23', 'tipe_akun' => 'Liabilitas Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-1500', 'nama_akun' => 'Uang Muka Penjualan', 'tipe_akun' => 'Liabilitas Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            
            // LIABILITAS JANGKA PANJANG
            ['kode_akun' => '2-2000', 'nama_akun' => 'Liabilitas Jangka Panjang', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-2100', 'nama_akun' => 'Utang Bank', 'tipe_akun' => 'Liabilitas Jk. Panjang', 'saldo_normal' => 'Kredit'],
            
            // EKUITAS
            ['kode_akun' => '3-1000', 'nama_akun' => 'Ekuitas', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3-1100', 'nama_akun' => 'Modal Pemilik', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3-1200', 'nama_akun' => 'Prive Pemilik', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '3-1300', 'nama_akun' => 'Laba Ditahan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3-1400', 'nama_akun' => 'Ikhtisar Laba Rugi', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            
            // PENDAPATAN
            ['kode_akun' => '4-1000', 'nama_akun' => 'Pendapatan', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-1100', 'nama_akun' => 'Penjualan Barang', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-1200', 'nama_akun' => 'Retur Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '4-1300', 'nama_akun' => 'Potongan Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit'],
            
            // HARGA POKOK PENJUALAN
            ['kode_akun' => '5-1000', 'nama_akun' => 'Harga Pokok Penjualan', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-1100', 'nama_akun' => 'Harga Pokok Penjualan (HPP)', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-1200', 'nama_akun' => 'Beban Angkut Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-1300', 'nama_akun' => 'Potongan Pembelian', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Kredit'],
            
            // BEBAN OPERASIONAL
            ['kode_akun' => '6-1000', 'nama_akun' => 'Beban Operasional', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1100', 'nama_akun' => 'Beban Gaji & Tunjangan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1200', 'nama_akun' => 'Beban Iklan & Promosi', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1300', 'nama_akun' => 'Beban Angkut Penjualan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1400', 'nama_akun' => 'Beban Sewa', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1500', 'nama_akun' => 'Beban Listrik, Air, Internet', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1600', 'nama_akun' => 'Beban Penyusutan Aset', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1700', 'nama_akun' => 'Beban Pemeliharaan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1800', 'nama_akun' => 'Beban Perlengkapan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-1900', 'nama_akun' => 'Beban Umum Lainnya', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            
            // PENDAPATAN LAINNYA
            ['kode_akun' => '8-1000', 'nama_akun' => 'Pendapatan Lainnya', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '8-1100', 'nama_akun' => 'Pendapatan Bunga', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '8-1200', 'nama_akun' => 'Pendapatan Jasa Giro', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit'],
            
            // BEBAN LAINNYA
            ['kode_akun' => '9-1000', 'nama_akun' => 'Beban Lainnya', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '9-1100', 'nama_akun' => 'Beban Bunga Bank', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '9-1200', 'nama_akun' => 'Beban Administrasi Bank', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '9-1300', 'nama_akun' => 'Pajak Final UMKM (0.5%)', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
        ];

        $this->insertAccounts($accounts);
    }

    /**
     * Seed additional COA for Manufacturing companies
     */
    private function seedManufakturCoa(): void
    {
        $accounts = [
            // PERSEDIAAN MANUFAKTUR (menggantikan 1-1400)
            ['kode_akun' => '1-1401', 'nama_akun' => 'Persediaan Manufaktur', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1410', 'nama_akun' => 'Persediaan Bahan Baku', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1420', 'nama_akun' => 'Persediaan Bahan Penolong', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1430', 'nama_akun' => 'Persediaan Barang Dalam Proses', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1440', 'nama_akun' => 'Persediaan Barang Jadi', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1450', 'nama_akun' => 'Persediaan Suku Cadang Pabrik', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            
            // BIAYA BAHAN BAKU
            ['kode_akun' => '5-1001', 'nama_akun' => 'Biaya Bahan Baku', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-1101', 'nama_akun' => 'Pemakaian Bahan Baku', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-1201', 'nama_akun' => 'Selisih Harga Bahan Baku', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            
            // BIAYA TENAGA KERJA LANGSUNG
            ['kode_akun' => '5-2000', 'nama_akun' => 'Biaya Tenaga Kerja Langsung', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-2100', 'nama_akun' => 'Upah Buruh Produksi', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-2200', 'nama_akun' => 'Tunjangan & Lembur Buruh', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            
            // BIAYA OVERHEAD PABRIK
            ['kode_akun' => '5-3000', 'nama_akun' => 'Biaya Overhead Pabrik (BOP)', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3100', 'nama_akun' => 'Pemakaian Bahan Penolong', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3200', 'nama_akun' => 'Gaji Supervisor/Mandor Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3300', 'nama_akun' => 'Listrik & Air Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3400', 'nama_akun' => 'Penyusutan Mesin & Gedung Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3500', 'nama_akun' => 'Pemeliharaan Mesin Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3600', 'nama_akun' => 'Sewa Gedung Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3700', 'nama_akun' => 'Asuransi Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3800', 'nama_akun' => 'Limbah & Kebersihan Pabrik', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-3900', 'nama_akun' => 'BOP Dibebankan (Alokasi)', 'tipe_akun' => 'Biaya Produksi / HPP', 'saldo_normal' => 'Kredit'],
            
            // HPP BARANG JADI
            ['kode_akun' => '5-4000', 'nama_akun' => 'HPP Barang Jadi', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-4100', 'nama_akun' => 'Harga Pokok Penjualan', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-4200', 'nama_akun' => 'Selisih Stok Opname (Produksi)', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit'],
        ];

        $this->insertAccounts($accounts);
    }

    /**
     * Seed additional COA for Agriculture (PSAK 69) companies
     */
    private function seedPsak69Coa(): void
    {
        $accounts = [
            // ASET BIOLOGIS (LANCAR)
            ['kode_akun' => '1-1402', 'nama_akun' => 'Aset Biologis (Lancar)', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1411', 'nama_akun' => 'Tanaman Semusim (Padi/Jagung)', 'tipe_akun' => 'Aset Biologis', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1421', 'nama_akun' => 'Hewan Ternak Potong (Siap Jual)', 'tipe_akun' => 'Aset Biologis', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-1431', 'nama_akun' => 'Produk Agrikultur (Saat Panen)', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            
            // ASET BIOLOGIS (TIDAK LANCAR)
            ['kode_akun' => '1-2500', 'nama_akun' => 'Aset Biologis (Tidak Lancar)', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2510', 'nama_akun' => 'Tanaman Kehutanan (Jati/Sengon)', 'tipe_akun' => 'Aset Biologis', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2520', 'nama_akun' => 'Hewan Ternak Bibit/Indukan', 'tipe_akun' => 'Aset Biologis', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2530', 'nama_akun' => 'Tanaman Produktif (Sawit/Karet)', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-2540', 'nama_akun' => 'Akum. Penyusutan Tan. Produktif', 'tipe_akun' => 'Akumulasi Penyusutan', 'saldo_normal' => 'Kredit'],
            
            // KEUNTUNGAN PENILAIAN WAJAR
            ['kode_akun' => '4-2000', 'nama_akun' => 'Keuntungan Penilaian Wajar', 'tipe_akun' => 'Header', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-2100', 'nama_akun' => 'Keuntungan Perubahan Nilai Wajar', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-2200', 'nama_akun' => 'Keuntungan Kelahiran Ternak', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit'],
            
            // BIAYA LANGSUNG BUDIDAYA
            ['kode_akun' => '5-2001', 'nama_akun' => 'Biaya Langsung Budidaya', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-2101', 'nama_akun' => 'Beban Pakan Ternak', 'tipe_akun' => 'HPP / Biaya Produksi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-2201', 'nama_akun' => 'Beban Pupuk & Obat-obatan', 'tipe_akun' => 'HPP / Biaya Produksi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '5-2301', 'nama_akun' => 'Beban Tenaga Kerja Tani', 'tipe_akun' => 'HPP / Biaya Produksi', 'saldo_normal' => 'Debit'],
            
            // KERUGIAN PENILAIAN WAJAR
            ['kode_akun' => '6-3000', 'nama_akun' => 'Kerugian Penilaian Wajar', 'tipe_akun' => 'Header', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-3100', 'nama_akun' => 'Kerugian Perubahan Nilai Wajar', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-3200', 'nama_akun' => 'Kerugian Kematian Ternak/Tanaman', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
        ];

        $this->insertAccounts($accounts);
    }

    /**
     * Insert accounts, skipping duplicates
     */
    private function insertAccounts(array $accounts): void
    {
        foreach ($accounts as $account) {
            $account['created_at'] = now();
            $account['updated_at'] = now();
            
            DB::table('akun')->updateOrInsert(
                ['kode_akun' => $account['kode_akun']],
                $account
            );
        }
    }
}
