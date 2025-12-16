<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'fixed_price',
        'price_per_mile',
        'price_per_hour',
        'price_per_load',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
