<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Property;
use App\Models\RoomApartment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
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
            # variable declaration
            if($request->property_type)
               $property_type_condition = " and property_type_id = ".$request->property_type;
            $where_condition = "status = ".PUBLISHED_PROPERTY." and country_id = ".$countryDetails->id.@$property_type_condition;
            $searchedPropertys = Property::whereRaw($where_condition)->get();
         }

         # return
         return ApiResponse::returnSuccessData($searchedPropertys);
      }
   }
}
