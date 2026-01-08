<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'is_active', 'pricing_type',];

    public function drivers()
    {
        return $this->belongsToMany(User::class, 'driver_services', 'service_id', 'driver_id')
            ->withPivot('pricing_type', 'price', 'is_available')
            ->withTimestamps();
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
