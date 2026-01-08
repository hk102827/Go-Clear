<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
     use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'address', 'postcode', 'profile_image', 'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relations
    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class, 'user_id');
    }

    public function driverServices()
    {
        return $this->hasMany(DriverService::class, 'driver_id');
    }

    public function jobsAsCustomer()
    {
        return $this->hasMany(Job::class, 'customer_id');
    }

    public function jobsAsDriver()
    {
        return $this->hasMany(Job::class, 'driver_id');
    }

    public function transactionsAsCustomer()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }

    public function transactionsAsDriver()
    {
        return $this->hasMany(Transaction::class, 'driver_id');
    }

    public function ratingsAsCustomer()
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }

    public function ratingsAsDriver()
    {
        return $this->hasMany(Rating::class, 'driver_id');
    }

    public function driverEarnings()
    {
        return $this->hasOne(DriverEarning::class, 'driver_id');
    }
        // Customer ke jobs
    public function customerJobs()
    {
        return $this->hasMany(Job::class, 'customer_id');
    }

    // Driver ke jobs (future use)
    public function driverJobs()
    {
        return $this->hasMany(Job::class, 'driver_id');
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

       public function services()
    {
        return $this->belongsToMany(Service::class, 'driver_services', 'driver_id', 'service_id')
                    ->withPivot('pricing_type', 'price', 'is_available')
                    ->withTimestamps();
    }

    protected $casts = [
    'created_at' => 'datetime',
];

public function getFormattedCreatedAtAttribute(): string
{
    $dt = $this->created_at;
    if ($dt instanceof \DateTimeInterface) {
        return $dt->format('M d, Y');
    }
    return 'N/A';
}

public function driverPricing()
{
    return $this->hasOne(DriverPricing::class, 'driver_id');
}

public function driverAvailability()
{
    return $this->hasOne(DriverAvailability::class, 'driver_id');
}


}