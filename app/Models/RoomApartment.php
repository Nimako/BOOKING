<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomApartment extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','common_room_amenity_id','image_ids','image_paths','room_size',
       'total_guest_capacity','total_bathrooms','num_of_rooms','updated_by','created_by','status'];
}
