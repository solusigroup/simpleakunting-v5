<?php

namespace Database\Seeders;

use App\Models\CentralUser;
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
        // Create default central superuser if not exists
        if (!CentralUser::where('nama_user', 'administrator')->exists()) {
            CentralUser::create([
                'nama_user' => 'administrator',
                'password_hash' => Hash::make('5@8@12Yaa'),
                'role' => 'superuser',
                'jabatan' => 'Central Administrator',
            ]);
            $this->command->info("Central superuser 'administrator' created successfully.");
        }
    }
}
