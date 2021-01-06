<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\CommonRoomAmenities;
use App\Models\Facility;
use App\Models\HotelDetails;
use App\Models\HotelOtherDetails;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\ApartmentDetail;
use App\Models\RoomDetails;
use App\Models\SubPolicy;
use App\Traits\ApiResponse;
use App\Traits\ImageProcessor;
use Database\Seeders\AmenitiesSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                  # variable declaration
                  $img = $uuid = $room_name = $total_guest_capacity = $total_bathrooms = $num_of_rooms = $price_list = array();
                  if(!empty(@$detailss['image_paths']))
                     $images = implode(STRING_GLUE, @$detailss['image_paths']);

                  // check if insert or update
                  if (empty($detailss['id']))
                     $uuid = array('uuid' => Uuid::uuid6());
                  if (@$detailss['room_name'])
                     $room_name = array('room_name' => $detailss['room_name']);
                  if (@$detailss['total_guest_capacity'])
                     $total_guest_capacity = array('total_guest_capacity' => $detailss['total_guest_capacity']);
                  if (@$detailss['total_bathrooms'])
                     $total_bathrooms = array('total_bathrooms' => $detailss['total_bathrooms']);
                  if (@$detailss['num_of_rooms'])
                     $num_of_rooms = array('num_of_rooms' => $detailss['num_of_rooms']);
                  if (@$images)
                     $img = array('image_paths' => @$images);
                  if (@$detailss['price_list'])
                     $price_list = array('price_list' => json_encode($detailss['price_list']));
                  if (@$detailss['similiar_rooms'])
                     $price_list = array('similiar_rooms' => json_encode($detailss['similiar_rooms']));

                  $apartmentDetailsInfo = array_merge($uuid,$room_name,$total_guest_capacity,$total_bathrooms,$num_of_rooms,$img,@$price_list);
                  if(!empty($apartmentDetailsInfo)) {
                     $apartmentDetailsInfo['property_id'] = $searchedProperty->id;
                     $room = ApartmentDetail::updateOrCreate($condition = ['uuid' => @$detailss['id']], $apartmentDetailsInfo);
                  }

                  // roomDetails
                  if(!empty($detailss['room_details']))
                  {
                     if(empty($room))
                        $room = ApartmentDetail::where(['uuid' => $detailss['id']])->first();

                     foreach ($detailss['room_details'] as $detail) {
                        $generatedUuid = $roomid = $room_name = $bed_types = $added_amenities = array();
                        # conditional variable
                        if(empty($detail['id']))
                           $generatedUuid = ['uuid' => Uuid::uuid6()];
                        if(!empty($room))
                           $roomid = array('room_id' => @$room->id);
                        if(!empty($detail['name']))
                           $room_name = array('room_name' => $detail['name']);
                        if(!empty($detail['bed_details']))
                           $bed_types = array('bed_types' => json_encode($detail['bed_details']));
                        if(!empty($detail['added_amenities']))
                           $added_amenities = array('added_amenities' => $detail['added_amenities']);
                        if(!empty($roomDetails = array_merge($generatedUuid,$roomid,$room_name,$bed_types,$added_amenities)))
                           RoomDetails::updateOrCreate($condition = ['uuid' => @$detail['id']], $roomDetails);
                     }
                     //return $roomDetails;

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
               }
            }

            # if amenities added to request
            if(!empty($request->amenities)) {
               if($roomArray = ApartmentDetail::where(['property_id' => $searchedProperty->id])->get()) {
                  foreach ($roomArray as $room) {
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
               }
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
         //'userid' => "required|exists:useraccount,id"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         $generalImages = array();
         // if property record found // 'created_by' => $request->userid
         $searchedProperty = Property::with('details')->where(['uuid' => $request->id])->first();

         // repopulating various room images as property images
         foreach ($searchedProperty->details as $images) {
            $generalImages =  @array_merge($generalImages, $images['image_pathss']);
         }

         $searchedProperty->all_property_images = $generalImages;

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
            if(!empty($request->serve_breakfast))
               $addToRequestObj['serve_breakfast'] = $request->serve_breakfast;
            if(!empty($request->languages_spoke))
               $addToRequestObj['languages_spoken'] = implode(STRING_GLUE, (array)$request->languages_spoke);
            if(!empty($request->property_type_id))
               $addToRequestObj['property_type_id'] = PropertyType::where(['uuid' => $request->property_type_id])->first()->id;

            // adding to request obj
            $request->merge($addToRequestObj);
            // saving property info
            $propertyUpdateResponse = $searchedProperty->update($request->all());

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
               $hotelDetails =  $request->details[0]['room_details'][0];
               // added factors
               $hotelDetailsSave = [
                  'created_by' =>  $request->created_by,
                  'property_id' => $searchedProperty->id,
                  'room_name' => $hotelDetails['room_name'],
                  'custom_room_name' => $hotelDetails['custom_room_name'],
                  'smoking_policy' => $hotelDetails['smoking_policy'],
                  'similiar_rooms' => $hotelDetails['similiar_rooms'],
                  'total_guest_capacity' => $hotelDetails['total_guest_capacity'],
                  'dimension' => $hotelDetails['dimension'],
                  'bed_types' => json_encode($hotelDetails['bed_details']),
                  'price' => json_encode($hotelDetails['pricelist']),
                  'added_amenities' => (empty(@$hotelDetails['added_amenities'])) ? null : json_encode(@$hotelDetails['added_amenities'])
               ];

               //return $hotelDetailsSave;

               if($searchedRecord = HotelDetails::where(['room_name' => $hotelDetails['room_name'], 'property_id' => $searchedProperty->id])->first()) {
                  $hotelResult = $result =  HotelDetails::find($searchedRecord->id);
                  $result->update($hotelDetailsSave);
               }
               else
                  $hotelResult = HotelDetails::create($hotelDetailsSave);
            }

            // saving other hotel details
            $HotelOtherDetails = [
               'created_by' => $request->created_by,
               'property_id' => $searchedProperty->id,
               'hotel_details_id' => $hotelResult->id,
               'listed_on' => $request->listed_on,
               'star_rating' => $request->star_rating,
               'own_multiple_hotel' => $request->own_multiple_hotel,
               'name_of_company_group_chain' => $request->name_of_company_group_chain,
               'use_channel_manager' => $request->use_channel_manager,
               'channel_manager_name' => $request->channel_manager_name,
               'parking_options' => json_encode($request->parking_options),
               'extra_bed_options' => json_encode($request->extra_bed_options),
            ];
            if($searchedOtherDetails = HotelOtherDetails::where(['property_id' => $searchedProperty->id])->first())
               $searchedOtherDetails->update($HotelOtherDetails);
            else
               HotelOtherDetails::create($HotelOtherDetails);

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

         if(!empty($request->property_type_id))
            $request->merge(['property_type_id' => PropertyType::where(['uuid' => $request->property_type_id])->first()->id]);

         // saving data
         if($searchedProperty = Property::where(['name' => $request->name])->first()) {
            $searchedProperty->update($request->all());
            $responseData = $searchedProperty;
         }
         else {
            $request->request->add(['uuid' => Uuid::uuid6()]);
            $responseData = Property::create($request->all());
         }
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
