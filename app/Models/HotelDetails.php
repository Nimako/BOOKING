<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelDetails extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','room_name','custom_room_name','price','smoking_policy', 'bed_types','similiar_rooms',
       'added_amenities','dimension','image_paths','total_guest_capacity','parking_options','created_by','updated_by'];

   protected $hidden = ['id','created_at','created_by','updated_at','updated_by','status','property_id','price'];

   protected $appends = ['bed_type_options','price_list'];

   public function getBedTypeOptionsAttribute()
   {
      return $this->attributes['bed_type_options'] = json_decode($this->bed_types);
   }

   public function getPriceListAttribute()
   {
      return $this->attributes['price_list'] = json_decode($this->price);
   }
}
