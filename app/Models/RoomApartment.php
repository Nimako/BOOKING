<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomApartment extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','common_room_amenity_id','image_ids','image_paths','room_size',
       'total_guest_capacity','total_bathrooms','num_of_rooms','updated_by','created_by','status'
    ];
    protected $hidden = ['id','common_room_amenity_id','image_ids','image_paths','created_at','updated_at','status','updated_by','created_by','property_id'];
    protected $appends = ['amenities','room_details','price_list','image_pathss'];


   public function getImagePathssAttribute()
   {
      $tempVar = explode(STRING_GLUE, $this->image_paths);
      foreach ($tempVar as $url) {
         $paths[] = asset('storage/app/public/'.$url);
      }
      return $this->attributes['image_pathss'] = $paths;
   }

    public function getAmenitiesAttribute()
   {
      if($searchedCommRmAmenities = CommonRoomAmenities::find($this->common_room_amenity_id))
         return $this->attributes['amenities'] = explode(STRING_GLUE, $searchedCommRmAmenities->popular_amenity_text);
   }

   public function getRoomDetailsAttribute()
   {
      return $this->attributes['room_details'] = RoomDetails::where(['room_id' => $this->id])->get();
   }

   public function getPriceListAttribute()
   {
      if($searchedRoom = RoomPrices::where(['room_id' => $this->id])->first()) {
         $guestOccupancy = explode(STRING_GLUE, $searchedRoom->guest_occupancy);
         $discount = explode(STRING_GLUE, $searchedRoom->discount);
         $amount = explode(STRING_GLUE, $searchedRoom->amount);

         for ($i=0; $i<sizeof($guestOccupancy); $i++) {
            $responseData[] = [
               'guest_occupancy' => $guestOccupancy[$i],
               'discount' => $discount[$i],
               'amount' => $amount[$i],
            ];
         }

         return $this->attributes['price_list'] = $responseData;
      }
    }
}
