<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Get setting value by key with caching.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        // Cache for 1 hour instead of forever
        $all = Cache::remember('app_settings_cache', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        return $all[$key] ?? $default;
    }
}

if (! function_exists('clear_settings_cache')) {
    /**
     * Clear settings cache.
     *
     * @return void
     */
    function clear_settings_cache()
    {
        Cache::forget('app_settings_cache');
    }
}