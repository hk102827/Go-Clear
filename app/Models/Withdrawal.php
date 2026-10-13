<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'amount',
        'status',
        'bank_details_id',
        'admin_id',
        'rejection_reason',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function bankDetails()
    {
        return $this->belongsTo(DriverBankDetail::class, 'bank_details_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'withdrawal_id');
    }
}