<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id', 'driving_license_front', 'driving_license_back', 'vehicle_photo',
        'number_plate', 'insurance_proof', 'waste_carrier_license', 'vehicle_type', 
        'vehicle_make_model','registration_number','manufacture_year','fuel_type',
        'verification_status', 'rejection_reason', 'verified_at',
        'is_online', 'rating', 'total_jobs',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function services()
    // {
    //     return $this->hasMany(DriverService::class, 'driver_id', 'user_id');
    // }
public function services()
{
    return $this->belongsToMany(
        Service::class,
        'driver_services',  // pivot table
        'driver_id',        // pivot column pointing to DriverProfile.user_id
        'service_id',       // pivot column pointing to Service.id
        'user_id',          // parent key on DriverProfile
        'id'                // local key on Service
    )->withPivot('pricing_type', 'price', 'is_available')
     ->withTimestamps();
}



    

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function isInProgress()
    {
        return $this->verification_status === 'in_progress';
    }

    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    public function earnings()
    {
        return $this->hasOne(DriverEarning::class, 'driver_id');
    }


    public function pricing()
{
    return $this->hasOne(\App\Models\DriverPricing::class, 'driver_id', 'user_id');
}

}
