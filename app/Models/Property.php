<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','property_type_id','name','text_location','geolocation','street_address_1','street_address_2','postal_code',
       'country_id','city','area','about_us','summary_text','primary_telephone','secondary_telephone','email','website','nearby_locations',
       'serve_breakfast','languages_spoken','images_ids','images_paths','current_onboard_stage','created_by','status','num_of_floors',
       'own_multiple_property','name_of_company_group_chain','use_channel_manager','channel_manager_name','star_rating'
    ];

    protected $hidden = ['created_at','updated_at','updated_by',];
    protected $appends = ['property_type_text','text_status','facilities','policies'];

   public function Details()
   {
      return $this->hasMany('App\Models\ApartmentDetail')->where('status','<>', 5);
   }

   public function getFacilitiesAttribute()
   {
      if($searchedCommRmFacilitis = CommonPropertyFacility::find($this->id))
         return $this->attributes['facilities'] = Facility::wherein('id', explode(STRING_GLUE, $searchedCommRmFacilitis->facility_ids))->get(['name','icon_class']);
   }

   public function getPoliciesAttribute()
   {
      if($searchedCommRmFacilitis = CommonPropertyPolicy::find($this->id))
         return $this->attributes['policies'] = explode(STRING_GLUE, $searchedCommRmFacilitis->sub_policy_text);
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
