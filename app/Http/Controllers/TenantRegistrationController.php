<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
     * Create a new tenant.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:tenants,id'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $tenant = Tenant::create([
            'id' => Str::lower($validated['tenant_id']),
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'email' => $validated['email'] ?? null,
        ]);

        // Create domain (subdomain only — InitializeTenancyBySubdomain resolves by subdomain part)
        $tenant->domains()->create([
            'domain' => $validated['tenant_id'],
        ]);

        $centralDomain = $request->getHost();
        return redirect()->route('central.tenants.index')
            ->with('success', "Tenant '{$validated['nama_perusahaan']}' berhasil dibuat! Akses via: {$validated['tenant_id']}.{$centralDomain}");
    }

    /**
     * List all tenants (admin panel).
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->orderBy('created_at', 'desc')->get();
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
}
