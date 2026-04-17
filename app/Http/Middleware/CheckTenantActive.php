<?php
/**
 * SimpleAkunting Middleware
 * Memastikan tenant yang sedang diakses berstatus AKTIF.
 * Jika tidak aktif, akses akan diblokir dengan pesan 403 Forbidden.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mendapatkan tenant saat ini dari stancl/tenancy global helper
        $tenant = tenant();

        if ($tenant) {
            // Periksa property is_active pada model Tenant
            if (isset($tenant->is_active) && $tenant->is_active == false) {
                abort(403, "Akses Diblokir: Perusahaan '{$tenant->nama_perusahaan}' saat ini berstatus NONAKTIF. Silakan hubungi Administrator pusat untuk informasi lebih lanjut.");
            }
        }

        return $next($request);
    }
}
