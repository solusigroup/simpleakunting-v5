<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\CentralAuthController;
use App\Http\Controllers\CentralUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Central / Single-Tenant)
|--------------------------------------------------------------------------
|
| When TENANCY_ENABLED=false:
|   All business routes below are loaded as is (single-tenant mode).
|
| When TENANCY_ENABLED=true:
|   Only central routes (landing page, tenant registration, admin) are loaded here.
|   Business routes are loaded from routes/tenant.php with subdomain middleware.
|
*/

// =====================================================
// CENTRAL ROUTES (only when multi-tenant is enabled)
// =====================================================
if (config('app.tenancy_enabled')) {
    $centralDomains = config('tenancy.central_domains', []);

    foreach ($centralDomains as $index => $domain) {
        $isPrimary = ($index === 0);

        Route::domain($domain)->group(function () use ($isPrimary) {

            // Central landing page
            $route = Route::get('/', function () {
                if (!session()->has('landing_page_viewed')) {
                    try {
                        \App\Models\LandingPageView::create([
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);
                        session()->put('landing_page_viewed', true);
                    } catch (\Exception $e) {
                        logger()->error('Error recording landing page view: ' . $e->getMessage());
                    }
                }

                $viewsCount = 212;
                try {
                    $viewsCount += \App\Models\LandingPageView::count();
                } catch (\Exception $e) {
                    logger()->error('Error counting landing page views: ' . $e->getMessage());
                }

                return view('central.landing', compact('viewsCount'));
            });
            if ($isPrimary) $route->name('central.landing');

            // Central Admin Login
            Route::middleware(['web', 'guest:central'])->group(function () use ($isPrimary) {
                $rLogin = Route::get('login', [CentralAuthController::class, 'showLoginForm']);
                if ($isPrimary) $rLogin->name('central.login');

                Route::post('login', [CentralAuthController::class, 'login'])->middleware('throttle:5,1');
            });

            // Central Admin Routes (auth + superuser only)
            Route::middleware(['web', 'auth:central', 'role:superuser'])->group(function () use ($isPrimary) {
                $rLogout = Route::post('logout', [CentralAuthController::class, 'logout']);
                if ($isPrimary) $rLogout->name('central.logout');

                $rRegT = Route::get('register-tenant', [TenantRegistrationController::class, 'showForm']);
                if ($isPrimary) $rRegT->name('central.register-tenant');

                $rStoreT = Route::post('register-tenant', [TenantRegistrationController::class, 'store']);
                if ($isPrimary) $rStoreT->name('central.register-tenant.store');

                $rIdxT = Route::get('admin/tenants', [TenantRegistrationController::class, 'index']);
                if ($isPrimary) $rIdxT->name('central.tenants.index');

                $rShowT = Route::get('admin/tenants/{id}', [TenantRegistrationController::class, 'show']);
                if ($isPrimary) $rShowT->name('central.tenants.show');

                $rEditT = Route::get('admin/tenants/{id}/edit', [TenantRegistrationController::class, 'edit']);
                if ($isPrimary) $rEditT->name('central.tenants.edit');

                $rUpdT = Route::put('admin/tenants/{id}', [TenantRegistrationController::class, 'update']);
                if ($isPrimary) $rUpdT->name('central.tenants.update');

                $rDelT = Route::delete('admin/tenants/{id}', [TenantRegistrationController::class, 'destroy']);
                if ($isPrimary) $rDelT->name('central.tenants.destroy');

                // Central User Management
                $rIdxU = Route::get('admin/users', [CentralUserController::class, 'index']);
                if ($isPrimary) $rIdxU->name('central.users.index');

                $rCreU = Route::get('admin/users/create', [CentralUserController::class, 'create']);
                if ($isPrimary) $rCreU->name('central.users.create');

                $rStoreU = Route::post('admin/users', [CentralUserController::class, 'store']);
                if ($isPrimary) $rStoreU->name('central.users.store');

                $rDelU = Route::delete('admin/users/{id}', [CentralUserController::class, 'destroy']);
                if ($isPrimary) $rDelU->name('central.users.destroy');

                $rEdiP = Route::get('admin/password', [CentralUserController::class, 'editPassword']);
                if ($isPrimary) $rEdiP->name('central.password.edit');

                $rUpdP = Route::put('admin/password', [CentralUserController::class, 'updatePassword']);
                if ($isPrimary) $rUpdP->name('central.password.update');

                // Workflow Documentation
                $rWorkflow = Route::get('admin/workflow', function () {
                    return view('central.admin.workflow');
                });
                if ($isPrimary) $rWorkflow->name('central.workflow');

                // Security Audit Documentation
                $rSecurity = Route::get('admin/security', function () {
                    return view('central.admin.security');
                });
                if ($isPrimary) $rSecurity->name('central.security');
            });
        });
    }
} else {

    // =====================================================
    // SINGLE-TENANT MODE (original routes, unchanged)
    // =====================================================

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        require __DIR__ . '/auth_shared.php';
    });
}
