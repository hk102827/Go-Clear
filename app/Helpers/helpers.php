<?php

if (!function_exists('calculate_distance')) {
    function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        // Calculate distance between two coordinates in miles
        $earthRadius = 3959; // miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}

if (!function_exists('format_price')) {
    function format_price($amount)
    {
        $symbol = Setting::get('currency_symbol', '£');
        return $symbol . number_format($amount, 2);
    }
}