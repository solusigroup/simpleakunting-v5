<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TenantBeaconMiddleware
{
    public function handle($request, Closure $next)
    {
        // 1. Tentukan interval pengiriman (Sekali dalam 24 jam)
        $cacheKey = 'tenant_heartbeat_sent_v2';

        if (!Cache::has($cacheKey)) {
            try {
                // 2. Kirim data detak jantung ke Server Pusat Bapak
                $response = Http::timeout(3)->post('https://simpleakunting.id/api/v1/heartbeat', [
                    'tenant_id' => config('tenancy.current_tenant_id'), // Contoh pengambilan ID
                    'domain' => $request->getHost(),
                    'app_version' => '5.0.0',
                    'php_version' => PHP_VERSION,
                    'secure_key' => hash('sha256', 'KunciRahasiaPanjangDanAcak!@#$%' . $request->getHost())
                ]);

                // 3. Simpan status di cache agar tidak kirim terus menerus
                if ($response->successful()) {
                    Cache::put($cacheKey, true, now()->addDay());
                } else {
                    // Jika gagal, coba lagi 5 menit kemudian (jangan beratkan loading setiap request)
                    Cache::put($cacheKey, false, now()->addMinutes(5));
                }
            } catch (\Exception $e) {
                // Biarkan gagal tanpa mengganggu user
            }
        }

        return $next($request);
    }
}