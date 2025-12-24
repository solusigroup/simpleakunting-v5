<?php

namespace Database\Seeders;

use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use Illuminate\Database\Seeder;

class JenisSimpanPinjamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jenis Simpanan
        $jenisSimpanan = [
            [
                'kode_simpanan' => 'SP',
                'nama_simpanan' => 'Simpanan Pokok',
                'tipe' => 'pokok',
                'bunga_pertahun' => 0,
                'akun_simpanan' => '2-1100',
                'akun_bunga' => null,
                'is_active' => true,
            ],
            [
                'kode_simpanan' => 'SW',
                'nama_simpanan' => 'Simpanan Wajib',
                'tipe' => 'wajib',
                'bunga_pertahun' => 0,
                'akun_simpanan' => '2-1200',
                'akun_bunga' => null,
                'is_active' => true,
            ],
            [
                'kode_simpanan' => 'SS',
                'nama_simpanan' => 'Simpanan Sukarela',
                'tipe' => 'sukarela',
                'bunga_pertahun' => 3.00,
                'akun_simpanan' => '2-1300',
                'akun_bunga' => '5-1100',
                'is_active' => true,
            ],
            [
                'kode_simpanan' => 'SD',
                'nama_simpanan' => 'Simpanan Deposito',
                'tipe' => 'deposito',
                'bunga_pertahun' => 6.00,
                'akun_simpanan' => '2-1400',
                'akun_bunga' => '5-1100',
                'is_active' => true,
            ],
        ];

        foreach ($jenisSimpanan as $item) {
            JenisSimpanan::updateOrCreate(
                ['kode_simpanan' => $item['kode_simpanan']],
                $item
            );
        }

        // Jenis Pinjaman
        $jenisPinjaman = [
            [
                'kode_pinjaman' => 'PP',
                'nama_pinjaman' => 'Pinjaman Produktif',
                'kategori' => 'produktif',
                'bunga_pertahun' => 12.00,
                'metode_bunga' => 'anuitas',
                'tenor_max' => 36,
                'plafon_max' => 50000000,
                'provisi_persen' => 1.00,
                'admin_fee' => 50000,
                'akun_piutang_pinjaman' => '1-1310',
                'akun_pendapatan_bunga' => '4-1100',
                'akun_pendapatan_provisi' => '4-1200',
                'akun_pendapatan_admin' => '4-1300',
                'is_active' => true,
            ],
            [
                'kode_pinjaman' => 'PK',
                'nama_pinjaman' => 'Pinjaman Konsumtif',
                'kategori' => 'konsumtif',
                'bunga_pertahun' => 15.00,
                'metode_bunga' => 'flat',
                'tenor_max' => 24,
                'plafon_max' => 30000000,
                'provisi_persen' => 1.50,
                'admin_fee' => 50000,
                'akun_piutang_pinjaman' => '1-1320',
                'akun_pendapatan_bunga' => '4-1100',
                'akun_pendapatan_provisi' => '4-1200',
                'akun_pendapatan_admin' => '4-1300',
                'is_active' => true,
            ],
            [
                'kode_pinjaman' => 'PD',
                'nama_pinjaman' => 'Pinjaman Darurat',
                'kategori' => 'darurat',
                'bunga_pertahun' => 10.00,
                'metode_bunga' => 'efektif',
                'tenor_max' => 12,
                'plafon_max' => 10000000,
                'provisi_persen' => 0.50,
                'admin_fee' => 25000,
                'akun_piutang_pinjaman' => '1-1330',
                'akun_pendapatan_bunga' => '4-1100',
                'akun_pendapatan_provisi' => '4-1200',
                'akun_pendapatan_admin' => '4-1300',
                'is_active' => true,
            ],
        ];

        foreach ($jenisPinjaman as $item) {
            JenisPinjaman::updateOrCreate(
                ['kode_pinjaman' => $item['kode_pinjaman']],
                $item
            );
        }
    }
}
