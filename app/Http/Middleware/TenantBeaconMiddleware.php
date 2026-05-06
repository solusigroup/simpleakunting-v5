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
            // 1. Tentukan interval pengiriman menggunakan Session (per user)
            // Karena driver cache (file/database) di server tidak mendukung cache tagging bawaan stancl/tenancy
            $cacheKey = 'tenant_heartbeat_sent_v8';
            
            if (!session()->has($cacheKey)) {
                $tenantId = function_exists('tenant') ? tenant('id') : null;
                $centralDomain = config('tenancy.central_domains')[0] ?? 'simpleakunting.id';
                $success = false;

                try {
                    // SKENARIO 1: Coba kirim via HTTP (Berfungsi untuk Tenant di Server Terpisah/Eksternal)
                    $response = Http::timeout(2)
                        ->post('https://' . $centralDomain . '/api/v1/heartbeat', [
                            'tenant_id' => $tenantId,
                            'domain' => $request->getHost(),
                            'app_version' => '5.0.0',
                            'php_version' => PHP_VERSION,
                            'secure_key' => hash('sha256', 'KunciRahasiaPanjangDanAcak!@#$%' . $request->getHost())
                        ]);
                    
                    if ($response->successful()) {
                        $success = true;
                    }
                } catch (\Throwable $e) {
                    // HTTP Gagal (Kemungkinan akibat NAT Loopback karena berada di server yang sama)
                }

                if (!$success && $tenantId) {
                    // SKENARIO 2: Fallback (Tulis langsung ke Database Central)
                    $centralConn = config('tenancy.database.central_connection', 'mysql');
                    
                    \App\Models\TenantHeartbeat::on($centralConn)->updateOrCreate(
                        ['tenant_id' => $tenantId],
                        [
                            'domain' => $request->getHost(),
                            'app_version' => '5.0.0',
                            'php_version' => PHP_VERSION,
                            'last_seen_at' => now(),
                        ]
                    );
                    
                    $success = true;
                }

                // 3. Simpan status di session agar tidak kirim terus menerus di setiap klik halaman
                if ($success) {
                    session()->put($cacheKey, true);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('TenantBeacon Error: ' . $e->getMessage() . ' di file ' . $e->getFile() . ':' . $e->getLine());
        }

        return $next($request);
    }
}