<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\CommonRoomAmenities;
use App\Models\Facility;
use App\Models\HotelDetail;
use App\Models\HotelDetails;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\ApartmentDetail;
use App\Models\RoomDetails;
use App\Models\RoomPrices;
use App\Models\SubPolicy;
use App\Traits\ApiResponse;
use App\Traits\ImageProcessor;
use Database\Seeders\AmenitiesSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class NewPropertyListingController extends Controller
{
   public function OnBoarding(Request $request)
   {
      // if property exists
      if(!empty($request->id))
      {
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
            //variable declaration
            $addToRequestObj['property_id'] = $searchedProperty->id;

            // property data pre-processing
            if(!empty($request->latitude) || !empty($request->longitude))
               return $addToRequestObj['geolocation'] = $request->latitude.','.$request->longitude;
            if(!empty($request->languages_spoke))
               $addToRequestObj['languages_spoken'] = implode(STRING_GLUE, (array)$request->languages_spoke);
            if(!empty($request->property_type_id))
               $addToRequestObj['property_type_id'] = PropertyType::where(['uuid' => $request->property_type_id])->first()->id;

            // adding to request obj
            $request->merge($addToRequestObj);
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
               $propertyCommonFacilities->facility_ids = trim(implode(STRING_GLUE, (array)$request->facilities), STRING_GLUE);
               $propertyCommonFacilities->facility_text = trim(implode(STRING_GLUE, (array)$facilitiesByName), STRING_GLUE);
               $propertyCommonFacilities->save();
            }

            # if policies added to request
            if(!empty($request->subpolicies))
            {
               $subPolicyText = $subPolicyIds = "";
               foreach ($request->subpolicies as $key => $value) {
                  if($subPolicy = SubPolicy::find($key)) {
                     $subPolicyIds .= $key.STRING_GLUE;
                     $subPolicyText .= $subPolicy->name.'='.$value.STRING_GLUE;
                  }
               }

               if($propertyCommonPolicies = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first())
                  $doNothing = "";
               else {
                  $propertyCommonPolicies = new CommonPropertyPolicy();
                  $propertyCommonPolicies->property_id = $searchedProperty->id;
               }

               // saving data
               $propertyCommonPolicies->sub_policy_ids = trim($subPolicyIds, STRING_GLUE);
               $propertyCommonPolicies->sub_policy_text = trim($subPolicyText, STRING_GLUE);
               $propertyCommonPolicies->save();
            }

            // apartment details
            if(!empty($request->details))
            {
               foreach ($request->details as $detailss) {
                  if(!empty(@$detailss['image_paths']))
                     $images = implode(STRING_GLUE, @$detailss['image_paths']);
                  $apartmentDetailsInfo= [
                     'room_name' => $detailss['room_name'],
                     'property_id' => $searchedProperty->id,
                     'total_guest_capacity' => $detailss['total_guest_capacity'],
                     'total_bathrooms' => $detailss['total_bathrooms'],
                     'num_of_rooms' => $detailss['num_of_rooms'],
                     'image_paths' => @$images
                  ];
                  /*if($room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first())
                     $room->update($apartmentDetailsInfo);
                  else*/
                     $room = ApartmentDetail::create($apartmentDetailsInfo);

                  // roomDetails
                  if(!empty($detailss['room_details']))
                  {
                     foreach ($detailss['room_details'] as $detail) {
                        $roomDetails[] = [
                           'room_id' => $room->id,
                           'room_name' => $detail['name'],
                           'bed_types' => json_encode($detail['bed_details']),
                           'added_amenities' => json_encode(@$detail['added_amenities']),
                        ];
                     }
                     RoomDetails::insert($roomDetails);
                     $roomDetails = array();
                  }

                  # image uploads
                  if($request->hasFile('images')) {
                     # searching for record
                     if($room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first()) {
                        // unlinking previous files
                        if($room->image_paths != null) {
                           $filePaths = explode(STRING_GLUE, $room->image_paths);
                           foreach ($filePaths as $filePath) {
                              unlink('storage/'.$filePath);
                           }
                        }
                        // upload new files
                        foreach ($request->file('images') as $image){
                           $fileStoragePaths[] =  ImageProcessor::UploadImage($image, $request->id);
                        }
                        # updating file upload field
                        ApartmentDetail::find($room->id)->update(['image_paths' => implode(STRING_GLUE, $fileStoragePaths)]);
                     }
                  }

                  # room prices
                  if(!empty($detailss['price_list'])) {
                     $guestOccupancy = $amount = $discounts = array();
                     $room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first();
                     foreach ($detailss['price_list'] as $pricesDetails) {
                        $guestOccupancy[] = $pricesDetails['guest_occupancy'];
                        $amount[] = $pricesDetails['amount'];
                        $discount[] = $pricesDetails['discount'];
                     }
                     // saving data
                     if($roomPrices = RoomPrices::where(['room_id' => $room->id])->first())
                        $doNothing = "";
                     else {
                        $roomPrices = new RoomPrices();
                        $roomPrices->room_id = $room->id;
                     }

                     $roomPrices->guest_occupancy = implode(STRING_GLUE, $guestOccupancy);
                     $roomPrices->amount = implode(STRING_GLUE, $amount);
                     $roomPrices->discount = implode(STRING_GLUE, $discount);
                     $roomPrices->save();
                  }
               }
            }

            # if amenities added to request
            if(!empty($request->amenities)) {
               $room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first();
               $searchedAmenities = Amenity::wherein('id', (array)$request->amenities)->get(['name'])->toArray();
               if($commonAmenities = CommonRoomAmenities::where(['room_id' => $room->id])->first())
                  $doNothing = "";
               else {
                  $commonAmenities = new CommonRoomAmenities();
                  $commonAmenities->room_id = $room->id;
               }

               // saving data
               $amenitiesByName = array_map(function($amenity) { return $amenity['name']; }, $searchedAmenities);
               $commonAmenities->popular_amenity_ids = trim(implode(STRING_GLUE, (array)$request->amenities), STRING_GLUE);
               $commonAmenities->popular_amenity_text = trim(implode(STRING_GLUE, (array)$amenitiesByName), STRING_GLUE);
               $commonAmenities->save();

               // updating link to room details
               $searchedRoom = ApartmentDetail::find($room->id);
               $searchedRoom->common_room_amenity_id = $commonAmenities->id;
               $searchedRoom->save();
            }

            // return statement
            return ApiResponse::returnSuccessData($data = ['id' => $searchedProperty->uuid, 'completed_onboard_stage' => $request->current_onboard_stage]);
         }
      }
      // new property
      else {
         // validation
         $rules = [
            'property_type_id' => "required|exists:property_types,uuid",
         ];
         $validator = Validator::make($request->all(), $rules, $customMessage = ['property_type_id.exists' => "Invalid Property Type Reference"]);
         if($validator->fails())
            return ApiResponse::returnErrorMessage($message = $validator->errors());

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

   public function FullOnBoardingDetails(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required|exists:properties,uuid",
         'userid' => "required|exists:useraccount,id"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         // if property record found
         $searchedProperty = Property::with('details')->where(['uuid' => $request->id, 'created_by' => $request->userid])->first();

         // return statement
         return ApiResponse::returnSuccessData($searchedProperty);
      }
   }

   public function HotelOnboarding(Request $request)
   {
      // if property exists
      if(!empty($request->id))
      {
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
            //variable declaration
            $addToRequestObj['property_id'] = $searchedProperty->id;

            // property data pre-processing
            if(!empty($request->latitude) || !empty($request->longitude))
               $addToRequestObj['geolocation'] = $request->latitude.','.$request->longitude;
            if(!empty($request->languages_spoke))
               $addToRequestObj['languages_spoken'] = implode(STRING_GLUE, (array)$request->languages_spoke);
            if(!empty($request->property_type_id))
               $addToRequestObj['property_type_id'] = PropertyType::where(['uuid' => $request->property_type_id])->first()->id;

            // adding to request obj
            $request->merge($addToRequestObj);
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
               $propertyCommonFacilities->facility_ids = trim(implode(STRING_GLUE, (array)$request->facilities), STRING_GLUE);
               $propertyCommonFacilities->facility_text = trim(implode(STRING_GLUE, (array)$facilitiesByName), STRING_GLUE);
               $propertyCommonFacilities->save();
            }

            # if policies added to request
            if(!empty($request->subpolicies))
            {
               $subPolicyText = $subPolicyIds = "";
               foreach ($request->subpolicies as $key => $value) {
                  if($subPolicy = SubPolicy::find($key)) {
                     $subPolicyIds .= $key.STRING_GLUE;
                     $subPolicyText .= $subPolicy->name.'='.$value.STRING_GLUE;
                  }
               }

               if($propertyCommonPolicies = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first())
                  $doNothing = "";
               else {
                  $propertyCommonPolicies = new CommonPropertyPolicy();
                  $propertyCommonPolicies->property_id = $searchedProperty->id;
               }

               // saving data
               $propertyCommonPolicies->sub_policy_ids = trim($subPolicyIds, STRING_GLUE);
               $propertyCommonPolicies->sub_policy_text = trim($subPolicyText, STRING_GLUE);
               $propertyCommonPolicies->save();
            }

            // apartment details
            if(!empty($request->details))
            {
               foreach ($request->details as $detailss) {
                  if(!empty(@$detailss['image_paths']))
                     $images = implode(STRING_GLUE, @$detailss['image_paths']);

                  $room = $searchedProperty;

                  // roomDetails
                  if(!empty($detailss['room_details']))
                  {
                     foreach ($detailss['room_details'] as $detail) {
                        $roomDetails[] = [
                           'property_id' => $searchedProperty->id,
                           'room_name' => $detail['name'],
                           'bed_types' => json_encode($detail['bed_details']),
                           'added_amenities' => (empty(@$detail['added_amenities'])) ? null : json_encode(@$detail['added_amenities']),
                        ];
                     }
                     // searching for update
                     if($searchedRecord = HotelDetails::where(['room_name' => $detail['name'], 'property_id' => $searchedProperty->id])->first())
                        HotelDetails::find($searchedRecord->id)->update($roomDetails[0]);
                     else
                        HotelDetails::insert($roomDetails);
                     $roomDetails = array();
                  }

                  # image uploads
                  /*if($request->hasFile('images')) {
                     # searching for record
                     if($room->image_paths != null) {
                        $filePaths = explode(STRING_GLUE, $room->image_paths);
                        foreach ($filePaths as $filePath) {
                           unlink('storage/'.$filePath);
                        }
                     }

                     // upload new files
                     foreach ($request->file('images') as $image){
                        $fileStoragePaths[] =  ImageProcessor::UploadImage($image, $request->id);
                     }
                     # updating file upload field
                     Property::find($room->id)->update(['image_paths' => implode(STRING_GLUE, $fileStoragePaths)]);
                  }*/

                  # room prices
                  if(!empty($detailss['price_list'])) {
                     $guestOccupancy = $amount = $discounts = array();
                     $room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first();
                     foreach ($detailss['price_list'] as $pricesDetails) {
                        $guestOccupancy[] = $pricesDetails['guest_occupancy'];
                        $amount[] = $pricesDetails['amount'];
                        $discount[] = $pricesDetails['discount'];
                     }
                     // saving data
                     if($roomPrices = RoomPrices::where(['room_id' => $room->id])->first())
                        $doNothing = "";
                     else {
                        $roomPrices = new RoomPrices();
                        $roomPrices->room_id = $room->id;
                     }

                     $roomPrices->guest_occupancy = implode(STRING_GLUE, $guestOccupancy);
                     $roomPrices->amount = implode(STRING_GLUE, $amount);
                     $roomPrices->discount = implode(STRING_GLUE, $discount);
                     $roomPrices->save();
                  }
               }
            }

            # if amenities added to request
            if(!empty($request->amenities)) {
               //$room = ApartmentDetail::where(['property_id' => $searchedProperty->id])->first();
               $searchedAmenities = Amenity::wherein('id', (array)$request->amenities)->get(['name'])->toArray();
               if($commonAmenities = CommonRoomAmenities::where(['property_id' => $searchedProperty->id])->first())
                  $doNothing = "";
               else {
                  $commonAmenities = new CommonRoomAmenities();
                  $commonAmenities->property_id = $searchedProperty->id;
               }

               // saving data
               $amenitiesByName = array_map(function($amenity) { return $amenity['name']; }, $searchedAmenities);
               $commonAmenities->popular_amenity_ids = trim(implode(STRING_GLUE, (array)$request->amenities), STRING_GLUE);
               $commonAmenities->popular_amenity_text = trim(implode(STRING_GLUE, (array)$amenitiesByName), STRING_GLUE);
               $commonAmenities->save();
            }

            // return statement
            return ApiResponse::returnSuccessData($data = ['id' => $searchedProperty->uuid, 'completed_onboard_stage' => $request->current_onboard_stage]);
         }
      }
      // new property
      else {
         // validation
         $rules = [
            'property_type_id' => "required|exists:property_types,uuid",
         ];
         $validator = Validator::make($request->all(), $rules, $customMessage = ['property_type_id.exists' => "Invalid Property Type Reference"]);
         if($validator->fails())
            return ApiResponse::returnErrorMessage($message = $validator->errors());

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

   public function CreateDummydata()
   {
      $a = new AmenitiesSeeder();
      $a->createNewProperty();
      $a->createApprovedProperty();

      return "success";
   }
}
