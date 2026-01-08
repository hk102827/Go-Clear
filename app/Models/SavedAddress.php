<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedAddress extends Model
{
    protected $fillable = [
        'user_id','booking_id','label','address','postcode','latitude','longitude','distance_meters',
        'duration_seconds',
    ];
}

