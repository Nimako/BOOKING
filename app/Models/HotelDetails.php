<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['room_name','bed_types','added_amenities','dimension','image_paths','custom_room_name','listed_on',
       'star_rating','smoking_policy',];
}
