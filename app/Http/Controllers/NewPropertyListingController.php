<?php

namespace App\Http\Controllers;

use App\Models\CommonPropertyFacility;
use App\Models\Facility;
use App\Models\Property;
use App\Models\RoomApartment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class NewPropertyListingController extends Controller
{
   public function stage1(Request $request)
   {
      // Validation
      $rules = [
         'name' => "required",
         'street_address' => "required",
         'property_type_id' => "required",
         'created_by' => "required"
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         $propertyDetails = [
            'name' => $request->name,
            'street_address' => $request->street_address,
            'property_type_id' => $request->property_type_id,
            'created_by' => $request->created_by,
            'current_onboard_stage' => "stage1",
            'uuid' => Uuid::uuid6()
         ];
         $property = Property::create($propertyDetails);
         $roomApartmentDetails = [
            'property_id' => $property->id,
            'num_of_rooms' => $request->num_of_rooms,
         ];
         RoomApartment::create($roomApartmentDetails);

         // return statement
         return ApiResponse::returnSuccessData(array('id' => $property->uuid));
      }
   }

   public function stage2(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required|exists:properties,uuid",
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Id"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         // return statement
         return ApiResponse::returnSuccessMessage($message = "Stage 2 Completed");
      }
   }

   public function stage3(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required|exists:properties,uuid",
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Id"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         if(!empty($request->facilities))
         {
            // getting facilities names using ids
            $searchedFacilities = Facility::wherein(['id' => implode(',', (array)$request->facilities)])->get();

            // if property record found
            if($propertyCommonFacilities = CommonPropertyFacility::where(['property_id'])->first())
               $doNothing = "";
            else
               $propertyCommonFacilities = new CommonPropertyFacility();

            // saving data
            $propertyCommonFacilities->facility_ids = implode(',', (array)$request->facilities);
            $propertyCommonFacilities->facility_text = implode(',', (array)$searchedFacilities);
            $propertyCommonFacilities->save();
         }
         else
            $doNothing = "";

         // return statement
         return ApiResponse::returnSuccessMessage($message = "Stage 3 Completed");
      }
   }
}
