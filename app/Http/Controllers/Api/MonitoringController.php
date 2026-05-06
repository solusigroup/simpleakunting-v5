<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantHeartbeat;

class MonitoringController extends Controller
{
    public function handleHeartbeat(Request $request)
    {
        // Validasi hash keamanan
        $expectedHash = hash('sha256', 'SECRET_SALT_BAPAK' . $request->domain);

        if ($request->secure_key !== $expectedHash) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid secure key.'
            ], 401);
        }

        // Simpan atau update data heartbeat tenant
        $heartbeat = TenantHeartbeat::updateOrCreate(
            ['tenant_id' => $request->tenant_id],
            [
                'domain' => $request->domain,
                'app_version' => $request->app_version,
                'php_version' => $request->php_version,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat logged successfully.',
            'data' => $heartbeat
        ], 200);
    }
}
