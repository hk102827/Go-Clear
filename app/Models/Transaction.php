<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id', 'job_id', 'customer_id', 'driver_id',
        'amount', 'admin_commission', 'driver_earning', 'payment_method', 'status'
    ];

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
