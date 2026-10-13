<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    public const STATUSES = [
        'pending',
        'accepted',
        'in_progress',
        'completed',
        'cancelled',
        'rejected',
    ];

    protected $fillable = [
        'job_number',
        'service_id',
        'customer_id',
        'driver_id',
        'status',
        'pickup_address',
        'pickup_postcode',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_address',
        'delivery_postcode',
        'delivery_latitude',
        'delivery_longitude',
        'description',
        'scheduled_at',
        'estimated_price',
        'final_price',
        'distance',
        'duration',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate job_number AFTER insert so $job->id exists — no race condition
        static::created(function (Job $job) {
            if (empty($job->job_number)) {
                $job->update([
                    'job_number' => 'GC-'
                        . $job->created_at->format('Ymd')
                        . '-'
                        . str_pad($job->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });

        // Guard against invalid status values (defense in depth)
        static::saving(function (Job $job) {
            if ($job->isDirty('status') && !in_array($job->status, self::STATUSES, true)) {
                throw new \InvalidArgumentException("Invalid job status: {$job->status}");
            }
        });
    }

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

    public function requests()
    {
        return $this->hasMany(\App\Models\JobRequest::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function paymentIntent()
    {
        return $this->hasOne(PaymentIntent::class);
    }
}