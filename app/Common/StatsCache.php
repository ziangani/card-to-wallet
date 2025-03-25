<?php

namespace App\Common;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatsCache
{
    const CACHE_KEY_PREFIX = 'system_stats_';


    public static function getCacheKey($startDate, $endDate, $type)
    {
        return self::CACHE_KEY_PREFIX . $type . '_' . $startDate . '_' . $endDate;
    }

    public static function getStats($startDate, $endDate, $type, $generator)
    {
        $cacheKey = self::getCacheKey($startDate, $endDate, $type);

        // If cache exists and is not expired, return cached data
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Generate fresh stats
        $stats = $generator();
        //30mins on local 5mins in production
        $DEFAULT_CACHE_TTL = (env('app_env') == 'production') ? 300 : 1800;
        // Cache the results
        Cache::put($cacheKey, $stats, Carbon::now()->addSeconds($DEFAULT_CACHE_TTL));

        return $stats;
    }

}
