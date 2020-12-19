<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','own_multiple_hotel','name_of_company_group_chain','use_channel_manager',
       'channel_manager_name','room_name','custom_room_name','listed_on','star_rating','smoking_policy', 'bed_types',
       'added_amenities','dimension','image_paths'];
}
