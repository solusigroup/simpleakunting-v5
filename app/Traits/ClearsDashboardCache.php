<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsDashboardCache
{
    public static function bootClearsDashboardCache()
    {
        $clearCache = function () {
            if (function_exists('tenant') && tenant('id')) {
                Cache::forget('dashboard_data_' . tenant('id'));
            }
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
