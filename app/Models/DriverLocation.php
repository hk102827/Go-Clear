<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'driver_profile_id',
        'lat',
        'lng',
        'heading',
        'speed',
        'recorded_at',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'heading' => 'integer',
        'speed' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function driverProfile()
    {
        return $this->belongsTo(DriverProfile::class);
    }
}