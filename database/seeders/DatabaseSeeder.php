<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

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
            $defaultPassword = Str::random(12);
            User::create([
                'nama_user' => 'admin',
                'password_hash' => Hash::make($defaultPassword),
                'role' => 'superuser',
                'jabatan' => 'Administrator',
            ]);
            $this->command->info("Default admin created. Password: {$defaultPassword}");
            $this->command->warn("⚠️  Segera ganti password ini setelah login pertama!");
        }

        $this->call([
            AkunSeeder::class,
            PerusahaanSeeder::class,
        ]);
    }
}

