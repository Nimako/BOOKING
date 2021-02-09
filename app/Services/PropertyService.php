<?php


namespace App\Services;


use App\Models\Property;

class PropertyService
{

   public static function getPropertyDetails($propertyID)
   {
      $searchedProperty = Property::find($propertyID);
      switch ($searchedProperty->property_type_id) {
         case APARTMENT:
            return Property::with('details')->find($searchedProperty->id);
            break;
         case HOTELS:
            return Property::with('hotel, hotel_details')->find($searchedProperty->id);
      }
   }
}
