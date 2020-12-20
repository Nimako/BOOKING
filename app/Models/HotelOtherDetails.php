<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelOtherDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','own_multiple_hotel','name_of_company_group_chain','use_channel_manager',
       'channel_manager_name','listed_on','star_rating','parking_options','created_by','updated_by'
    ];
}
