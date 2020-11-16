<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Property;
use App\Models\RoomApartment;
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
         if($searchedPropertys = Property::where(['created_by' => $request->userid])->get()) {
            foreach ($searchedPropertys as $property) {
               # other searches
               $apartmentDetails = RoomApartment::where(['property_id' => $property->id])->first();

               $responseData[] = [
                  'uuid' => $property->uuid,
                  'property_type_text' => $property->property_type_text,
                  'name' => $property->name,
                  'street_address_1' => $property->street_address_1,
                  'display_img' => @$apartmentDetails->image_pathss[0],
                  'num_of_guest' => @$apartmentDetails->total_guest_capacity,
                  'num_of_rooms' => @$apartmentDetails->num_of_rooms
               ];
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
         'country' => "required"
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         #
         if($countryDetails = Country::where(['iso' => $request->country])->first()) {
            # variable conditions
            if($request->property_type)
               $property_type_condition = " and a.property_type_id = ".$request->property_type;
            if($request->num_of_guests)
               $numofguest_condition = " and b.total_guest_capacity = ".$request->num_of_guests;
            if($request->num_of_rooms)
               $numofrooms_condition = " and b.num_of_rooms = ".$request->num_of_rooms;

            $where_condition = "a.status = ".PUBLISHED_PROPERTY." and a.country_id = ".$countryDetails->id. @$property_type_condition. @$numofguest_condition. @$numofrooms_condition;
            $query = "select * from properties a left join room_apartments b on b.property_id = a.id where ".$where_condition;
            $searchedPropertys = DB::select($query);


         }

         # return
         return ApiResponse::returnSuccessData($searchedPropertys);
      }
   }
}
