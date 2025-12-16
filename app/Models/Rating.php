<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['job_id', 'customer_id', 'driver_id', 'rating', 'review'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
