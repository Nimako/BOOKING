<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','property_type_id','name','text_location','geolocation','street_address_1','street_address_2','postal_code',
       'country_id','city','about_us','summary_text','primary_telephone','secondary_telephone','email','website','nearby_locations',
       'serve_breakfast','languages_spoken','images_ids','images_paths','current_onboard_stage','created_by'
    ];

    protected $hidden = ['created_at','updated_at','updated_by'];

   public function Details()
   {
      return $this->hasOne('App\Models\RoomApartment');
   }
}
