<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('perusahaan')->updateOrInsert(
            ['id' => 1],
            [
                'nama_perusahaan' => 'PT. Contoh Perusahaan',
                'alamat' => 'Jl. Contoh No. 123, Jakarta',
                'telepon' => '021-12345678',
                'email' => 'info@contoh.com',
                'akun_piutang_default' => '1-10100', // Piutang Usaha
                'akun_utang_default' => '2-10100',   // Utang Usaha
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
