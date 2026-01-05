<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default superuser if not exists
        if (!User::where('nama_user', 'admin')->exists()) {
            User::create([
                'nama_user' => 'admin',
                'password_hash' => Hash::make('admin123'),
                'role' => 'superuser',
                'jabatan' => 'Administrator',
            ]);
        }

        $this->call([
            AkunSeeder::class,
            PerusahaanSeeder::class,
        ]);
    }
}

