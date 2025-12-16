<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'platform_name' => 'required|string|max:255',
            'platform_email' => 'required|email',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'currency' => 'required|string|max:10',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        Cache::forget('app_settings_cache');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    // NEW METHOD - App Name Update
    public function updateAppName(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
        ]);

        Setting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => $validated['app_name']]
        );

        return redirect()->back()->with('success', 'App name updated successfully.');
    }
}