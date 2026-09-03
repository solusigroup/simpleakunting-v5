<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsDashboardCache
{
    public static function bootClearsDashboardCache()
    {
        $clearCache = function () {
            $tenantId = (function_exists('tenant') && tenant('id')) ? tenant('id') : 'central';
            Cache::forget('dashboard_data_' . $tenantId);
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
