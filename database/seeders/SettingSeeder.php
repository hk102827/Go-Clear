<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Go Clear', 'type' => 'text'],
            ['key' => 'admin_commission', 'value' => '15', 'type' => 'number'],
            ['key' => 'currency', 'value' => 'GBP', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value' => '£', 'type' => 'text'],
            ['key' => 'distance_unit', 'value' => 'miles', 'type' => 'text'],
            ['key' => 'support_email', 'value' => 'support@goclear.com', 'type' => 'text'],
            ['key' => 'support_phone', 'value' => '+44 1234 567890', 'type' => 'text'],
            ['key' => 'enable_notifications', 'value' => 'true', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}