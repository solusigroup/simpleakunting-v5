<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'pelanggan' => 'Pelanggan',
            'pemasok' => 'Pemasok',
            'persediaan' => 'Persediaan',
            'akun' => 'Akun (COA)',
            'pos' => 'Point of Sales',
            'penjualan' => 'Penjualan',
            'pembelian' => 'Pembelian',
            'jurnal' => 'Jurnal Umum',
            'penerimaan' => 'Penerimaan Kas',
            'pembayaran' => 'Pembayaran Kas',
            'kas' => 'Transaksi Kas',
            'bukubesar' => 'Buku Besar',
            'laporan' => 'Laporan Akuntansi',
            'anggota' => 'Anggota Koperasi',
            'simpanan' => 'Simpanan',
            'pinjaman' => 'Pinjaman',
            'manufacturing' => 'Manufaktur',
            'agriculture' => 'Pertanian',
            'aset_tetap' => 'Aset Tetap',
            'users' => 'Manajemen User',
            'role_management' => 'Manajemen Role',
            'cabang' => 'Kantor Cabang',
            'unit_usaha' => 'Unit Usaha',
            'perusahaan' => 'Profil Perusahaan',
            'audit_trail' => 'Audit Trail',
            'database' => 'Manajemen Database',
        ];

        $actions = [
            'view' => 'Lihat',
            'create' => 'Tambah',
            'edit' => 'Ubah',
            'delete' => 'Hapus',
        ];

        foreach ($modules as $moduleSlug => $moduleName) {
            foreach ($actions as $actionSlug => $actionName) {
                Permission::updateOrCreate(
                    ['name' => "$moduleSlug.$actionSlug"],
                    [
                        'display_name' => "$actionName $moduleName",
                        'module' => $moduleSlug
                    ]
                );
            }
        }

        // Create Default System Roles
        $roles = [
            'superuser' => [
                'display_name' => 'Superuser',
                'description' => 'Akses penuh ke semua fitur sistem.',
                'is_system' => true,
            ],
            'admin' => [
                'display_name' => 'Administrator',
                'description' => 'Pengelola harian operasional perusahaan.',
                'is_system' => true,
            ],
            'manajer' => [
                'display_name' => 'Manajer',
                'description' => 'Pengawas transaksi dan laporan.',
                'is_system' => true,
            ],
            'staff' => [
                'display_name' => 'Staff / Akunting',
                'description' => 'Input data transaksi harian.',
                'is_system' => true,
            ],
            'kasir' => [
                'display_name' => 'Kasir',
                'description' => 'Melayani transaksi POS.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleSlug => $roleData) {
            Role::updateOrCreate(
                ['name' => $roleSlug],
                $roleData
            );
        }

        // Auto-assign permissions to roles based on legacy expectations
        $allPermissions = Permission::all();
        
        // Superuser gets everything (handled by isSuperuser check too)
        $superuser = Role::where('name', 'superuser')->first();
        $superuser->permissions()->sync($allPermissions->pluck('id'));

        // Admin gets most things except database management
        $admin = Role::where('name', 'admin')->first();
        $adminPerms = $allPermissions->filter(function ($p) {
            return !str_contains($p->name, 'database');
        });
        $admin->permissions()->sync($adminPerms->pluck('id'));

        // Manajer: View all, Create/Edit most, No delete for core accounting
        $manajer = Role::where('name', 'manajer')->first();
        $manajerPerms = $allPermissions->filter(function ($p) {
            $isDelete = str_contains($p->name, '.delete');
            $isAdminOnlyModule = in_array($p->module, ['users', 'role_management', 'database', 'perusahaan']);
            return !$isAdminOnlyModule && (!$isDelete || in_array($p->module, ['pelanggan', 'pemasok', 'persediaan']));
        });
        $manajer->permissions()->sync($manajerPerms->pluck('id'));

        // 4. Migrate existing users to use role_id based on legacy role string
        $users = \App\Models\User::whereNull('role_id')->get();
        foreach ($users as $user) {
            $role = Role::where('name', $user->role)->first();
            if ($role) {
                $user->update(['role_id' => $role->id]);
            }
        }
    }
}
