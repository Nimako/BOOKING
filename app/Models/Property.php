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
    ];

    protected $hidden = ['created_at','updated_at','updated_by',];
    protected $appends = ['property_type_text','text_status','facilities','policies', 'country_name','booked' ];

   public function HotelDetails()
   {
      return $this->hasMany('App\Models\HotelDetails')->where('status','<>', DELETED_PROPERTY);
   }

   public function OtherHotelDetails()
   {
      return $this->hasMany('App\Models\HotelOtherDetails')->where('status','<>', DELETED_PROPERTY);
   }

   public function Details()
   {
      return $this->hasMany('App\Models\ApartmentDetail')->where('status','<>', DELETED_PROPERTY);
   }

   public function getFacilitiesAttribute()
   {
      if($searchedCommRmFacilitis = CommonPropertyFacility::where(['property_id' => $this->id])->first())
         return $this->attributes['facilities'] = Facility::wherein('id', explode(STRING_GLUE, $searchedCommRmFacilitis->facility_ids))->get(['name','icon_class']);
   }

   public function getPoliciesAttribute()
   {
      if($searchedCommRmFacilitis = CommonPropertyPolicy::where(['property_id' => $this->id])->first()){
         $explodedPolicies = explode(STRING_GLUE, $searchedCommRmFacilitis->sub_policy_text);
         foreach ($explodedPolicies as $explodedPolicy){
            $exp = explode('=', $explodedPolicy);
            $responseData[] = [
               $exp[0] => $exp[1]
            ];
         }
      }

      return $this->attributes['policies'] = @$responseData;
   }

   public function getPropertyTypeTextAttribute()
   {
      return $this->attributes['property_type_text'] = PropertyType::find($this->property_type_id)->name;
   }

   public function getTextStatusAttribute()
   {
      return $this->attributes['text_status'] = PROPERTY_STATUSES[$this->status];
   }

   public function getCountryNameAttribute()
   {
      return Country::find($this->country_id)->name;
   }

   public function getBookedAttribute($key)
   {
      return $this->attributes['booked'] = Booking::where(['property_id' => $this->id])->get()->count();
   }

   public function getImagesPathsAttribute($val)
   {
      $responseData = array();
      switch ($this->property_type_id) {
         case HOTELS :
            $allHotelImages = HotelDetails::where(['property_id' => $this->id])->get(['image_paths'])->toArray();
            if(!empty($allHotelImages)) {
               foreach ($allHotelImages as $allHotelImage) {
                  $responseData = array_merge($responseData, $allHotelImage['image_paths']);
               }
            }
            break;
      }

      return $this->images_paths = $responseData;
   }
}
