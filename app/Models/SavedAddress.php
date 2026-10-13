<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedAddress extends Model
{
    protected $table = 'saved_addresses';
    protected $fillable = [
        'user_id','booking_id','label','address','postcode','latitude','longitude','distance_meters',
        'duration_seconds',
    ];
}

