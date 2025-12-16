<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverEarning extends Model
{
    protected $fillable = ['driver_id', 'total_earnings', 'available_balance', 'withdrawn_amount'];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
