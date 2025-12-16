<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

public function boot()
{
    if (request()->header('x-forwarded-proto') == 'https') {
        URL::forceScheme('https');
    }
    
    Schema::defaultStringLength(191);
    
    // Load helpers
    require_once app_path('Helpers/helpers.php');

    // Only load settings if table exists
    if (Schema::hasTable('settings')) {
        $settings = Cache::remember('app_settings_cache', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        // Set Laravel config value (so config('app.name') works)
        if (isset($settings['app_name'])) {
            config(['app.name' => $settings['app_name']]);
        }

        // Share with all views as $appName
        view()->share('appName', $settings['app_name'] ?? config('app.name'));
    }
}

}