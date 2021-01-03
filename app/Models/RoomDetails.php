<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDetails extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','room_id','room_name','similar_rooms','bed_types','added_amenities','dimension'];

   protected $hidden = ['id','created_at','updated_at','status','room_id'];

   //protected $appends = ['bed_type_name'];

   public function getBedTypesAttribute($value)
   {
      $jsonData = json_decode($value);
      $tempVar = array_map(function($data){return $data->bed_type;}, $jsonData);

      return BedType::find($tempVar);
   }

   public function getAddedAmenityTextAttribute()
   {
      if(!empty($this->added_amenity_text))
         return $this->added_amenity_text = json_decode($this->added_amenity_text);
   }
}
