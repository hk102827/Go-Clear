<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'customer_id', 'driver_id', 'service_id',
        'pickup_address', 'pickup_postcode', 'pickup_latitude', 'pickup_longitude',
        'delivery_address', 'delivery_postcode', 'delivery_latitude', 'delivery_longitude',
        'description', 'scheduled_at', 'status', 'estimated_price', 'final_price', 'distance', 'duration', 'cancellation_reason'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];



    protected static function boot()
{
    parent::boot();

    static::creating(function ($job) {
        $job->job_number = 'GC-' . now()->format('Ymd') . '-' . str_pad(
            (Job::max('id') + 1),
            5,
            '0',
            STR_PAD_LEFT
        );
    });
}

}
