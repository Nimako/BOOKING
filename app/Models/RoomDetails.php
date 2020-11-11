<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDetails extends Model
{
    use HasFactory;


   protected $hidden = ['id','created_at','updated_at','status','updated_by','created_by','room_id','bed_type','added_amenity_ids'];

   protected $appends = ['bed_type_name'];

   public function getBedTypeNameAttribute()
   {
      return $this->attributes['bed_type_name'] = BedType::find($this->bed_type)->name;
   }

   public function getAddedAmenityTextAttribute()
   {
      if(!empty($this->added_amenity_text))
         return $this->added_amenity_text = explode(STRING_GLUE, $this->added_amenity_text);
   }
}
