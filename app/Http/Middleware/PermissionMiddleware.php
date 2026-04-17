<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            $isCentralDomain = in_array($request->getHost(), config('tenancy.central_domains', []));
            return redirect()->route($isCentralDomain ? 'central.login' : 'login');
        }

        // Check if user has the specific permission
        // Note: superuser check is inside hasPermission method
        if (!$user->hasPermission($permission)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin ('. $permission .') untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
