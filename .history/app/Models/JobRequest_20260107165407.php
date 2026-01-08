<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    protected $fillable = [
        'job_id', 'driver_id', 'status', 'sent_at', 'responded_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function job() {
        return $this->belongsTo(Job::class);
    }

    public function driver() {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
