<?php

namespace App\Http\Controllers;

use App\Models\CommonPropertyFacility;
use App\Models\Facility;
use App\Models\Policy;
use App\Models\Property;
use App\Models\SubPolicy;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class NewPropertyListingController extends Controller
{
   public function onBoarding(Request $request)
   {
      // if property exists
      if(!empty($request->id))
      {
         // validation
         $rules = [
            'id' => "required|exists:properties,uuid",
            'current_onboard_stage' => "required"
         ];
         $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
         if($validator->fails()) {
            return ApiResponse::returnErrorMessage($message = $validator->errors());
         }
         else {
            // if property record found
            $searchedProperty = Property::where(['uuid' => $request->id])->first();

            // property data pre-processing
            if(!empty($request->latitude) || !empty($request->longitude))
               $request->request->add(['geolocation' => $request->latitude.','.$request->longitude]);
            if(!empty($request->languages_spoke))
               $request->request->add(['languages_spoken' => implode('**', (array)$request->languages_spoke)]);

            // saving property info
            $propertyUpdateResponse = Property::find($searchedProperty->id)->update($request->all());

            # checking other parameters
            if(!empty($request->facilities)) {
               $searchedFacilities = Facility::wherein('id', (array)$request->facilities)->get(['name'])->toArray();
               if($propertyCommonFacilities = CommonPropertyFacility::where(['property_id' => $searchedProperty->id])->first())
                  $doNothing = "";
               else {
                  $propertyCommonFacilities = new CommonPropertyFacility();
                  $propertyCommonFacilities->property_id = $searchedProperty->id;
               }

               // saving data
               $facilitiesByName = array_map(function($facility) { return $facility['name']; }, $searchedFacilities);
               $propertyCommonFacilities->facility_ids = implode('**', (array)$request->facilities);
               $propertyCommonFacilities->facility_text = implode('**', (array)$facilitiesByName);
               $propertyCommonFacilities->save();
            }

            if(!empty($request->house_rules))
            {

            }

            // return statement
            return ApiResponse::returnSuccessData($data = ['id' => $searchedProperty->uuid, 'completed_onboard_stage' => $request->current_onboard_stage]);
         }
      }
      else {
         // data pre-processing
         $request->request->add(['uuid' => Uuid::uuid6()]);
         // saving data
         $responseData = Property::create($request->all());
         // return statement
         return ApiResponse::returnSuccessData(array('id' => $responseData->uuid, 'completed_onboard_stage' => "Stage1"));
      }
   }

   public function stage6(Request $request)
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
         if(!empty($request->house_rules))
         {
            // getting facilities names using ids
            return $searchedFacilities = SubPolicy::wherein('id', (array)$request->house_rules)->get(['name'])->toArray();

            // if property record found
            $searchedProperty = Property::where(['uuid' => $request->id])->first();
            if($propertyCommonFacilities = CommonPropertyFacility::where(['property_id' => $searchedProperty->id])->first())
               $doNothing = "";
            else {
               $propertyCommonFacilities = new CommonPropertyFacility();
               $propertyCommonFacilities->property_id = $searchedProperty->id;
            }

            // saving data
            $facilitiesByName = array_map(function($facility) { return $facility['name']; }, $searchedFacilities);
            $propertyCommonFacilities->facility_ids = implode('**', (array)$request->facilities);
            $propertyCommonFacilities->facility_text = implode('**', (array)$facilitiesByName);
            $propertyCommonFacilities->save();

            // saving current on-boarding stage
            $searchedProperty->current_onboard_stage = "stage3";
            $searchedProperty->save();
         }
         else
            $doNothing = "";

         // return statement
         return ApiResponse::returnSuccessMessage($message = "Stage 6 Completed");
      }
   }
}
