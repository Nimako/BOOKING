<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','property_type_id','name','text_location','geolocation','street_address_1','street_address_2','postal_code',
       'country_id','city','area','about_us','summary_text','primary_telephone','secondary_telephone','email','website','nearby_locations',
       'serve_breakfast','languages_spoken','images_ids','images_paths','current_onboard_stage','created_by','status','num_of_floors'
    ];

    protected $hidden = ['created_at','updated_at','updated_by',];
    protected $appends = ['property_type_text','text_status'];

   public function Details()
   {
      return $this->hasMany('App\Models\ApartmentDetail');
   }

   public function getPropertyTypeTextAttribute()
   {
      return $this->attributes['property_type_text'] = PropertyType::find($this->property_type_id)->name;
   }

   public function getTextStatusAttribute()
   {
      return $this->attributes['text_status'] = PROPERTY_STATUSES[$this->status];
   }
}
