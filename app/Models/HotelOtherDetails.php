<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelOtherDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','hotel_details_id','own_multiple_hotel','name_of_company_group_chain','use_channel_manager',
       'channel_manager_name','listed_on','star_rating','parking_options','extra_bed_options','created_by','updated_by'
    ];

    protected $hidden = ['id','created_at','created_by','updated_at','updated_by','status','property_id','hotel_details_id','parking_options','extra_bed_options'];

    protected $appends = ['parking_optionss','extra_bed_optionss'];


   public function getParkingOptionssAttribute()
   {
      return $this->attributes['parking_optionss'] = json_decode($this->parking_options);
   }

   public function getExtraBedOptionssAttribute()
   {
      return $this->attributes['extra_bed_optionss'] = json_decode($this->extra_bed_options);
   }
}
