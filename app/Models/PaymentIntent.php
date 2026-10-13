<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    protected $fillable = [
        'job_id',
        'customer_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'client_secret',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
