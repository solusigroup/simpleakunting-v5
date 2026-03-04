<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanSeeder extends Seeder
{
    /**
     * Seed perusahaan table with default blank data.
     */
    public function run(): void
    {
        DB::table('perusahaan')->updateOrInsert(
            ['id' => 1],
            [
                'nama_perusahaan' => 'Nama Perusahaan',
                'alamat' => '-',
                'telepon' => '-',
                'email' => '',
                'akun_piutang_default' => '1-10100',
                'akun_utang_default' => '2-10100',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
