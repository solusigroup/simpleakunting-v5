<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TenantBeaconMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // 1. Tentukan interval pengiriman (Sekali dalam 24 jam)
            $cacheKey = 'tenant_heartbeat_sent_v4';
            
            if (!Cache::has($cacheKey)) {
                // 2. Kirim data detak jantung ke Server Pusat Bapak
                // Ganti pengambilan tenant_id menggunakan fungsi bawaan stancl/tenancy
                $tenantId = function_exists('tenant') ? tenant('id') : null;
                
                $centralDomain = config('tenancy.central_domains')[0] ?? 'simpleakunting.id';
                
                // Gunakan 127.0.0.1 (localhost) untuk bypass masalah NAT Loopback / Hairpin NAT di server lokal,
                // sambil mengirimkan header Host asli agar diterima oleh Nginx/Apache.
                $response = Http::timeout(3)
                    ->withOptions(['verify' => false]) // Abaikan validasi SSL lokal
                    ->withHeaders(['Host' => $centralDomain])
                    ->post('http://127.0.0.1/api/v1/heartbeat', [
                        'tenant_id' => $tenantId,
                        'domain' => $request->getHost(),
                        'app_version' => '5.0.0',
                        'php_version' => PHP_VERSION,
                        'secure_key' => hash('sha256', 'KunciRahasiaPanjangDanAcak!@#$%' . $request->getHost())
                    ]);

                // 3. Simpan status di cache agar tidak kirim terus menerus
                if ($response->successful()) {
                    Cache::put($cacheKey, true, now()->addDay());
                } else {
                    // Jika gagal, coba lagi 5 menit kemudian
                    Cache::put($cacheKey, false, now()->addMinutes(5));
                }
            }
        } catch (\Throwable $e) {
            // Tangkap SEMUA jenis error (Exception maupun Fatal Error/TypeError) 
            // agar tidak menyebabkan halaman 500 bagi pengguna tenant.
            \Illuminate\Support\Facades\Log::error('TenantBeacon Error: ' . $e->getMessage() . ' di file ' . $e->getFile() . ':' . $e->getLine());
        }

        return $next($request);
    }
}