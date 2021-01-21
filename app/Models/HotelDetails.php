<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','property_id','room_name','custom_room_name','price','smoking_policy', 'bed_types','similiar_rooms',
       'added_amenities','dimension','image_paths','total_guest_capacity','parking_options','created_by','updated_by'];

   protected $hidden = ['created_at','created_by','updated_at','updated_by','property_id','price'];

   protected $appends = ['price_list'];

   public function getBedTypesAttribute($value)
   {
      $bedDetails = json_decode($value);
      foreach ($bedDetails as $bed){
         $responseData[] = [
            'bed_type' => BedType::find($bed->bed_type)->name,
            'bed_qty' => $bed->bed_qty
         ];
      }
      return $this->bed_type = $responseData;
   }

   public function getPriceListAttribute()
   {
      return $this->attributes['price_list'] = json_decode($this->price);
   }

   public function getImagePathsAttribute($value)
   {
      $explodedPaths = explode(STRING_GLUE, $value);
      $responseData = array_map(function($imagepath){
         return asset('storage/app/public/'.$imagepath);
      },$explodedPaths);
      return $this->image_paths = $responseData;
   }
}
