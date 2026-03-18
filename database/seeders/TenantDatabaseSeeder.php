<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database with initial data.
     * This runs automatically when a new tenant is created.
     */
    public function run(): void
    {
        // 1. Seed Chart of Accounts (CoA)
        $this->call(AkunSeeder::class);

        // 2. Seed Perusahaan with tenant registration data
        $tenant = tenant();
        DB::table('perusahaan')->updateOrInsert(
            ['id' => 1],
            [
                'nama_perusahaan' => $tenant->nama_perusahaan ?? 'Perusahaan Baru',
                'email' => $tenant->email ?? null,
                'alamat' => '-',
                'telepon' => '-',
                'akun_piutang_default' => '1-10100',
                'akun_utang_default' => '2-10100',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Create default admin user for the tenant
        if (User::count() === 0) {
            User::create([
                'nama_user' => 'admin',
                'password_hash' => Hash::make('admin123'),
                'role' => 'admin',
                'jabatan' => 'Administrator',
            ]);
            \Log::info("Tenant [{$tenant->id}] admin created. Default password: admin123");
        }
    }
}
