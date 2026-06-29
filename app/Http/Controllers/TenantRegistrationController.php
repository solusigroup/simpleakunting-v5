<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantRegistrationController extends Controller
{
    /**
     * Show tenant registration form.
     */
    public function showForm()
    {
        return view('central.register-tenant');
    }

    /**
     * Simpan tenant baru.
     * Jika sampai di method ini, data dipastikan SUDAH VALID & AMAN
     * berkat StoreTenantRequest.
     *
     * CATATAN: Tidak menggunakan DB::transaction() karena Tenant::create()
     * dari stancl/tenancy secara internal membuat database baru, switch
     * koneksi, dan menjalankan migrasi — yang akan mengacaukan transaksi luar.
     */
    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();

        $tenant = Tenant::create([
            'id'               => $validated['tenant_id'], // Sudah di-lowercase otomatis oleh Request
            'nama_perusahaan'  => $validated['nama_perusahaan'],
            'email'            => $validated['email'] ?? null,
            'admin_username'   => $validated['admin_username'],
            'admin_password'   => $validated['admin_password'],
        ]);

        // Logika domain
        $domainToSave = (!empty($validated['use_custom_domain']) && !empty($validated['custom_domain'])) 
            ? $validated['custom_domain'] 
            : $validated['tenant_id'];

        $tenant->domains()->create([
            // Simpan subdomain atau domain kustom
            'domain' => $domainToSave,
        ]);

        return redirect()->route('central.tenants.index')
            ->with('success', "Tenant '{$validated['nama_perusahaan']}' berhasil dibuat!");
    }

    /**
     * List all tenants (admin panel).
     */
    public function index()
    {
        $tenants = Tenant::select('tenants.*', 'tenant_heartbeats.last_seen_at', 'tenant_heartbeats.domain as heartbeat_domain')
            ->leftJoin('tenant_heartbeats', 'tenants.id', '=', 'tenant_heartbeats.tenant_id')
            ->with('domains')
            ->orderBy('tenants.created_at', 'desc')
            ->get();
            
        foreach ($tenants as $tenant) {
            try {
                $tenant->run(function () use ($tenant) {
                    $tenant->trx_count = \App\Models\Jurnal::count();
                });
            } catch (\Exception $e) {
                $tenant->trx_count = '-';
            }
        }
            
        return view('central.admin.tenants', compact('tenants'));
    }

    /**
     * Delete a tenant and its database.
     */
    public function destroy(Request $request, string $id)
    {
        // Block in production
        if (app()->environment('production')) {
            return redirect()->route('central.tenants.index')
                ->with('error', 'Penghapusan tenant tidak diizinkan di lingkungan production.');
        }

        $tenant = Tenant::findOrFail($id);

        // Double confirmation: user must type tenant ID
        if ($request->input('confirm_name') !== $id) {
            return redirect()->route('central.tenants.index')
                ->with('error', "Konfirmasi gagal. Ketik tepat \"{$id}\" untuk menghapus tenant.");
        }

        $tenant->delete();

        return redirect()->route('central.tenants.index')
            ->with('success', "Tenant '{$id}' berhasil dihapus beserta database-nya.");
    }

    /**
     * Tampilkan detail tenant.
     */
    public function show($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        return view('central.admin.tenants_show', compact('tenant'));
    }

    /**
     * Form edit tenant.
     */
    public function edit($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        return view('central.admin.tenants_edit', compact('tenant'));
    }

    /**
     * Update data tenant & sinkronisasi ke user admin di tenant DB.
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'is_active'       => 'required|boolean',
            'admin_username'  => 'required|string|max:50',
            'admin_password'  => 'nullable|string|min:8',
        ]);

        // 1. Update Central Metadata
        $tenant->update([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'email'           => $validated['email'],
            'is_active'       => $validated['is_active'],
            'admin_username'  => $validated['admin_username'],
        ]);

        // 2. Sinkronisasi ke Database Tenant
        $tenant->run(function () use ($validated) {
            $user = \App\Models\User::where('role', 'admin')->first();
            
            // Jika user admin tidak ditemukan (misal seeder gagal dijalankan di awal karena --force),
            // jalankan seeder sekarang dengan opsi --force agar database terisi lengkap.
            if (!$user) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\TenantDatabaseSeeder',
                        '--force' => true,
                    ]);
                    // Cari lagi setelah seeding
                    $user = \App\Models\User::where('role', 'admin')->first();
                } catch (\Exception $e) {
                    \Log::error("Failed to seed tenant database during update sync: " . $e->getMessage());
                }
            }

            if ($user) {
                $updateData = [
                    'nama_user' => $validated['admin_username'],
                ];
                
                if (!empty($validated['admin_password'])) {
                    $updateData['password_hash'] = \Illuminate\Support\Facades\Hash::make($validated['admin_password']);
                }

                $user->update($updateData);
            } else {
                // Fallback jika seeder tetap tidak membuat user admin, buat secara manual
                $roleId = \App\Models\Role::where('name', 'admin')->value('id');
                \App\Models\User::create([
                    'nama_user'     => $validated['admin_username'],
                    'password_hash' => \Illuminate\Support\Facades\Hash::make($validated['admin_password'] ?? 'password'),
                    'role'          => 'admin',
                    'role_id'       => $roleId,
                    'jabatan'       => 'Administrator',
                ]);
            }
        });

        return redirect()->route('central.tenants.index')
            ->with('success', "Tenant '{$id}' berhasil diperbarui dan disinkronkan.");
    }
}
