<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverService extends Model
{
    protected $fillable = ['driver_id', 'service_id', 'pricing_type', 'price', 'is_available'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getPriceFormatted()
    {
        $prefix = '£' . number_format($this->price, 2);
        
        return match($this->pricing_type) {
            'per_mile' => $prefix . '/mile',
            'per_hour' => $prefix . '/hour',
            'per_load' => $prefix . '/load',
            default => $prefix,
        };
    }
}
