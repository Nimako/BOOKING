<?php


namespace App\Services;


use App\Models\Amenity;
use App\Models\ApartmentDetail;
use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\CommonRoomAmenities;
use App\Models\Facility;
use App\Models\Property;
use App\Models\RoomDetails;
use App\Models\SubPolicy;
use App\Traits\ApiResponse;
use App\Traits\ImageProcessor;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ApartmentService
{
   public static function saveNew(Request $request)
   {
      $searchedProperty = Property::where(['uuid' => $request->id])->first();

      switch ($request->current_onboard_stage) {
         case 'Stage2':
            $lat = $request->latitude ?? 0;
            $lng = $request->longitude ?? 0;
            $searchedProperty->geolocation = $lat . ',' . $lng;
            $searchedProperty->save();
            break;

         case 'Stage3' :
            $searchedFacilities = Facility::wherein('id', (array)$request->facilities)->get(['name'])->toArray();
            if ($propertyCommonFacilities = CommonPropertyFacility::where(['property_id' => $searchedProperty->id])->first())
               $doNothing = "";
            else {
               $propertyCommonFacilities = new CommonPropertyFacility();
               $propertyCommonFacilities->property_id = $searchedProperty->id;
            }

            // saving data
            $facilitiesByName = array_map(function ($facility) {
               return $facility['name'];
            }, $searchedFacilities);
            $propertyCommonFacilities->facility_ids = trim(implode(STRING_GLUE, (array)$request->facilities), STRING_GLUE);
            $propertyCommonFacilities->facility_text = trim(implode(STRING_GLUE, (array)$facilitiesByName), STRING_GLUE);
            $propertyCommonFacilities->save();
            break;

         case 'Stage4':
            $searchedProperty->update($request->all());
            break;

         case 'Stage5':
            $searchedProperty->languages_spoken = implode(STRING_GLUE, (array)$request->languages_spoke);
            $searchedProperty->save();
            break;

         case 'Stage6':
         case 'Stage7':
            $subPolicyText = $subPolicyIds = "";
            foreach ($request->subpolicies as $key => $value) {
               if ($subPolicy = SubPolicy::find($key)) {
                  $subPolicyIds .= $key . STRING_GLUE;
                  $subPolicyText .= $subPolicy->name . '=' . $value . STRING_GLUE;
               }
            }

            if ($propertyCommonPolicies = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first())
               $doNothing = "";
            else {
               $propertyCommonPolicies = new CommonPropertyPolicy();
               $propertyCommonPolicies->property_id = $searchedProperty->id;
            }

            // saving data
            $propertyCommonPolicies->sub_policy_ids = trim($subPolicyIds, STRING_GLUE);
            $propertyCommonPolicies->sub_policy_text = trim($subPolicyText, STRING_GLUE);
            $propertyCommonPolicies->save();
            break;

         case 'Stage8':
            foreach ($request->details as $detail) {
               if ($apartmentDetails = ApartmentDetail::where(['uuid' => @$detail['id']])->first())
                  $updateCondition = ['uuid' => $apartmentDetails->uuid];
               else
                  $updateCondition = ['uuid' => Uuid::uuid6()];

               $apartmentSaveData = array_merge($updateCondition, [
                  'property_id' => $searchedProperty->id,
                  'num_of_rooms' => $detail['num_of_rooms'],
                  'room_name' => $detail['room_name'],
                  'total_bathrooms' => $detail['total_bathrooms'],
                  'total_guest_capacity' => $detail['total_guest_capacity'],
               ]);

               $savedApartmentDetails = ApartmentDetail::updateOrCreate($updateCondition, $apartmentSaveData);

               if(!empty($detail['room_details'])) {
                  foreach ($detail['room_details'] as $room_detail) {
                     if(!empty($roomDetails = RoomDetails::where(['uuid' => $room_detail['id']])->first()))
                        $updateCondition = ['uuid' => $roomDetails->uuid];
                     else
                        $updateCondition = ['uuid' => Uuid::uuid6()];

                     $roomDetailSaveData = array_merge($updateCondition, [
                        'room_id' => $savedApartmentDetails->id,
                        'bed_types' => json_encode($room_detail['bed_details']),
                        'room_name' => $room_detail['name'],
                        'added_amenities' => @$room_detail['added_amenities'],
                        'dimension' => @$room_detail['dimension']
                     ]);
                     $savedRoomDetails = RoomDetails::updateOrCreate($updateCondition, $roomDetailSaveData);
                  }
               }
            }
            break;

         case 'Stage9':
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
            break;

         case 'Stage10':
            if($request->hasFile('images')) {
               # searching for record
               if($room = ApartmentDetail::where(['uuid' => $request->apartment_id])->first()) {
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
            break;

         case 'Stage11':
            $searchedApartment = ApartmentDetail::where(['uuid' => $request->apartment_id])->first();
            $searchedApartment->update(['price_list' => json_encode($request->price_list)]);
            break;

         case 'Stage12':
            $searchedProperty->update($request->all());
            break;
      }

      // updating stages
      $searchedProperty->update($request->all());

      return ApiResponse::returnRawData(Property::with('details')->where(['id' => $searchedProperty->id])->first());
   }
}
