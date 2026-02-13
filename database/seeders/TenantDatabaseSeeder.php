<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        // 2. Seed Perusahaan (Company Profile - placeholder)
        $this->call(PerusahaanSeeder::class);

        // 3. Create default admin user for the tenant
        if (User::count() === 0) {
            User::create([
                'nama_user' => 'admin',
                'password_hash' => Hash::make('password'),
                'role' => 'admin',
                'jabatan' => 'Administrator',
            ]);
        }
    }
}
