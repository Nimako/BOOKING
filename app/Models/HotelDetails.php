<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','room_name','custom_room_name','listed_on','star_rating','smoking_policy', 'bed_types',
       'added_amenities','dimension','image_paths','total_guest_capacity'];
}
