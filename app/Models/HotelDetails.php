<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','room_name','custom_room_name','price','smoking_policy', 'bed_types','similiar_rooms',
       'added_amenities','dimension','image_paths','total_guest_capacity','parking_options','created_by','updated_by'];
}
