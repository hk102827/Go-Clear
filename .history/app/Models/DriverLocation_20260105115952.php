<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'driver_id','lat','lng','heading','speed','recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
