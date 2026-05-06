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
            // 1. Tentukan interval pengiriman
            $cacheKey = 'tenant_heartbeat_sent_v6';
            
            if (!Cache::has($cacheKey)) {
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
                    // Berfungsi sempurna untuk Tenant yang berada di mesin server yang sama
                    $centralConn = config('tenancy.database.central_connection', 'mysql');
                    $db = \Illuminate\Support\Facades\DB::connection($centralConn)->table('tenant_heartbeats');
                    
                    if ($db->where('tenant_id', $tenantId)->exists()) {
                        $db->where('tenant_id', $tenantId)->update([
                            'domain' => $request->getHost(),
                            'app_version' => '5.0.0',
                            'php_version' => PHP_VERSION,
                            'last_seen_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        $db->insert([
                            'tenant_id' => $tenantId,
                            'domain' => $request->getHost(),
                            'app_version' => '5.0.0',
                            'php_version' => PHP_VERSION,
                            'last_seen_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    $success = true;
                }

                // 3. Simpan status di cache agar tidak kirim terus menerus
                if ($success) {
                    Cache::put($cacheKey, true, now()->addDay());
                } else {
                    Cache::put($cacheKey, false, now()->addMinutes(5));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('TenantBeacon Error: ' . $e->getMessage() . ' di file ' . $e->getFile() . ':' . $e->getLine());
        }

        return $next($request);
    }
}