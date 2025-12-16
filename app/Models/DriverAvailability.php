<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'days',
        'start_time',
        'end_time',
        'service_radius',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
