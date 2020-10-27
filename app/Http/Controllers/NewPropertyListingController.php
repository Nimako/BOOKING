<?php

namespace App\Http\Controllers;

use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\Facility;
use App\Models\Policy;
use App\Models\Property;
use App\Models\PropertyType;
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
         $stringGlue = "**";
         // validation
         $rules = [
            'id' => "required|exists:properties,uuid",
            'current_onboard_stage' => "required",
            'created_by' => "required"
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
               $request->request->add(['languages_spoken' => implode($stringGlue, (array)$request->languages_spoke)]);
            if(!empty($request->property_type_id))
               $request->merge(['property_type_id' => PropertyType::where(['uuid' => $request->property_type_id])->first()->id]);

            // saving property info
            $propertyUpdateResponse = Property::find($searchedProperty->id)->update($request->all());

            # if facilities added to request
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
               $propertyCommonFacilities->facility_ids = trim(implode($stringGlue, (array)$request->facilities), $stringGlue);
               $propertyCommonFacilities->facility_text = trim(implode($stringGlue, (array)$facilitiesByName), $stringGlue);
               $propertyCommonFacilities->save();
            }

            # if policies added to request
            if(!empty($request->subpolicies))
            {
               $subPolicyText = $subPolicyIds = "";
               foreach ($request->subpolicies as $key => $value) {
                  if($subPolicy = SubPolicy::find($key)) {
                     $subPolicyIds .= $key.$stringGlue;
                     $subPolicyText .= $subPolicy->name.'='.$value.$stringGlue;
                  }
               }

               if($propertyCommonPolicies = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first())
                  $doNothing = "";
               else {
                  $propertyCommonPolicies = new CommonPropertyPolicy();
                  $propertyCommonPolicies->property_id = $searchedProperty->id;
               }

               // saving data
               $propertyCommonPolicies->sub_policy_ids = trim($subPolicyIds, $stringGlue);
               $propertyCommonPolicies->sub_policy_text = trim($subPolicyText, $stringGlue);
               $propertyCommonPolicies->save();
            }

            // return statement
            return ApiResponse::returnSuccessData($data = ['id' => $searchedProperty->uuid, 'completed_onboard_stage' => $request->current_onboard_stage]);
         }
      }
      else {
         // data pre-processing
         $request->request->add(['uuid' => Uuid::uuid6()]);
         if(!empty($request->property_type_id))
            $request->merge(['property_type_id' => PropertyType::where(['uuid' => $request->property_type_id])->first()->id]);

         // saving data
         $responseData = Property::create($request->all());
         // return statement
         return ApiResponse::returnSuccessData(array('id' => $responseData->uuid, 'completed_onboard_stage' => "Stage1"));
      }
   }

   public function onBoardingDetails(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required|exists:properties,uuid",
         'current_onboard_stage' => "required"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         // if property record found
         $searchedProperty = Property::where(['uuid' => $request->id])->first();

         // return statement
         return ApiResponse::returnSuccessMessage($message = "Stage 6 Completed");
      }
   }
}
