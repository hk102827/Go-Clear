<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'is_approved',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'bank_details_id');
    }

    // Accessors
    public function getApprovalStatusAttribute()
    {
        if ($this->is_approved === null) {
            return 'pending';
        }
        return $this->is_approved ? 'approved' : 'rejected';
    }
}
