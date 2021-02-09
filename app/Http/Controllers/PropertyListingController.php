<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\CommonPropertyFacility;
use App\Models\CommonRoomAmenities;
use App\Models\Country;
use App\Models\Facility;
use App\Models\HotelDetails;
use App\Models\Property;
use App\Models\PropertyRating;
use App\Models\ApartmentDetail;
use App\Models\PropertyType;
use App\Models\RoomDetails;
use App\Services\PropertyService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyListingController extends Controller
{
   public function GetUserProperties(Request $request)
   {
      // Validation
      $rules = [
         'userid' => "required|exists:useraccount,id"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['userid.exists' => "Invalid User Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         # variable declaration
         $responseData = array();
         if($searchedPropertys = Property::where(['created_by' => $request->userid])->whereRaw('status != '.DELETED_PROPERTY)->get()) {
            foreach ($searchedPropertys as $property) { //return $property;
               # other searches
               switch ($property->property_type_id) {
                  case APARTMENT:
                     $apartmentDetails = ApartmentDetail::where(['property_id' => $property->id])->first();
                     $totalGuestCapacity = $apartmentDetails->total_guest_capacity;
                     $images = $apartmentDetails->image_pathss;
                     $num_of_rooms = @$apartmentDetails->num_of_rooms;
                     break;

                  case HOTELS :
                     $hotelDetails = HotelDetails::where(['property_id' => $property->id])->first();
                     $totalGuestCapacity = @$hotelDetails->total_guest_capacity;
                     $images = @$hotelDetails->image_paths;
                     $num_of_rooms = HotelDetails::where(['property_id' => $property->id])->get()->count();
                     break;
               }


               $responseData[] = [
                  'uuid' => $property->uuid,
                  'property_type_text' => $property->property_type_text,
                  'name' => $property->name,
                  'street_address_1' => $property->street_address_1,
                  'display_img' => @$images[0],
                  'num_of_guest' => @$totalGuestCapacity,
                  'num_of_rooms' => $num_of_rooms
               ];

               ## resetting values
               unset($images, $totalGuestCapacity);
            }
         }

         # return
         return ApiResponse::returnSuccessData($responseData);
      }
   }

   public function SearchProperty(Request $request)
   {
      // Validation
      $rules = [
         'country' => "required",
         'city' => "required",
         'search' => "required",
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         #
         $publishedProperties = array();
         if($countryDetails = Country::where(['iso' => $request->country])->first()) {
            # where conditions
            $where_conditions = "(a.status = ".PUBLISHED_PROPERTY." and a.country_id = {$countryDetails->id})";

            # query build and result
            $distinctQuery = "select distinct a.id from properties a where {$where_conditions}";
            $propertiesFound = DB::select($distinctQuery);

            if(!empty($propertiesFound))
            {
               # getting property ids
               $property_ids = array_map(function($data){ return $data->id;}, $propertiesFound);

               # search
               $searchedPropertys = Property::wherein('id', $property_ids)->get();
               foreach ($searchedPropertys as $property) {
                  if($foundString = strstr($property->city, $request->search) || $foundString = strstr($property->name, $request->search)) {
                     # variables
                     $propertyDetails = PropertyService::getPropertyDetails($property->id);
                     $geoData = explode(',', $propertyDetails->geolocation);
                     $propertyDetails->distance_from_current_position = ceil(PropertyListingController::distance($request->latitude,$request->longitude,@$geoData[0],@$geoData[1],'K'))." km";
                     $responseData['found'][] = $propertyDetails;
                  }
                  else {
                     # variables
                     $propertyDetails = PropertyService::getPropertyDetails($property->id);
                     $geoData = explode(',', $propertyDetails->geolocation);
                     $propertyDetails->distance_from_current_position = ceil(PropertyListingController::distance($request->latitude,$request->longitude,@$geoData[0],@$geoData[1],'K'))." km";
                     $responseData['alternatives'] = $propertyDetails;
                  }
               }
            }
         }

         # return
         return ApiResponse::returnSuccessData(@$responseData);
      }
   }

   public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
         return ($miles * 1.609344);
      } else if ($unit == "N") {
         return ($miles * 0.8684);
      } else {
         return $miles;
      }
   }

   public function booking(Request $request)
   {
      // Validation
      $rules = [
         'userid' => "required",
         'parent_property' => "required|exists:properties,uuid"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['parent_property.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         if(Booking::insert($request->all()))
            return ApiResponse::returnSuccessMessage($message = "Property Booked");
         else
            return ApiResponse::returnErrorMessage($message = "An Error Occurred");
      }
   }
}
