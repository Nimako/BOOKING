<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\ApartmentDetail;
use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\CommonRoomAmenities;
use App\Models\Country;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyRating;
use App\Models\PropertyType;
use App\Models\RoomDetails;
use App\Models\RoomPrices;
use App\Models\SubPolicy;
use App\Traits\ApiResponse;
use App\Traits\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use File;

class PropertyController extends Controller
{
   /**
    * Store a newly created resource in storage.
    *
    * @param Request $request
    * @return JsonResponse
    */
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
                  if(!empty(@$detailss['image_paths']))
                     $images = implode(STRING_GLUE, @$detailss['image_paths']);
                  $apartmentDetailsInfo= [
                     'uuid' => Uuid::uuid6(),
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

    /**
     * Duplicate a listing of the resource.
     *
     * @return Response
     */
    public function DuplicateProperty(Request $request)
    {
       // validation
       $rules = [
          'id' => "required|exists:properties,uuid",
          'duplicate_id' => "required"
       ];
       $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
       if($validator->fails()) {
          return ApiResponse::returnErrorMessage($message = $validator->errors());
       }
       else {
           // property info
           $searchedProperty = Property::where(['uuid' => $request->id])->first();
           /*$newProperty = $searchedProperty->replicate();
           $newProperty->uuid = Uuid::uuid6();
           $newProperty->save();*/

           switch ($searchedProperty->property_type_id) {
              case 1 :
                 /*// common property facilities
                 $searchedDetails = CommonPropertyFacility::where(['property_id' => $searchedProperty->id])->first();
                 $newDetails = $searchedDetails->replicate();
                 $newDetails->property_id = $searchedProperty->id;
                 $newDetails->save();*/

                 /*// common property policies
                 $searchedDetails = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first();
                 $newDetails = $searchedDetails->replicate();
                 $newDetails->property_id = $searchedProperty->id;
                 $newDetails->save();*/

                 // apartment details
                 $searchedDetails = ApartmentDetail::where(['uuid' => $request->duplicate_id])->get();
                 foreach ($searchedDetails as $details) {
                    // image duplication mgmt
                     $images = array_map(function($image){
                        $a = explode('storage/', $image);
                        $copy4rm = storage_path($a[1]);

                        $cpy_img = str_replace(' ', '_', substr($image, 0, strripos($image, '.'))."_dup_".microtime().".webp");
                        $a = explode('storage/', $cpy_img);
                        $copy2 = storage_path($a[1]);

                        File::copy($copy4rm, $copy2);
                        $imageReturned = explode('public/', $cpy_img);

                        return $imageReturned[1];
                     }, $details->image_pathss);

                     //return $images;

                    // duplicate apartment details
                    $apartmentDetails = [
                       'uuid' => Uuid::uuid6(),
                       'property_id' => $searchedProperty->id,
                       'room_name' => $details->room_name,
                       'total_guest_capacity' => $details->total_guest_capacity,
                       'total_bathrooms' => $details->total_bathrooms,
                       'num_of_rooms' => $details->num_of_rooms,
                       //'common_room_amenity_id' => $details->common_room_amenity_id,
                       'image_paths' => implode(STRING_GLUE, $images),
                    ];
                    $newlySaveApartment = ApartmentDetail::create($apartmentDetails);

                    // duplicating common room amenities
                    $searchedDetails = CommonRoomAmenities::find($details->common_room_amenity_id);
                    $newDetails = $searchedDetails->replicate();
                    $newDetails->property_id = $searchedProperty->id;
                    $newDetails->room_id = $newlySaveApartment->id;
                    $newDetails->save();

                    //updating apartment details
                    ApartmentDetail::find($newlySaveApartment->id)->update(['common_room_amenity_id' => $newDetails->id]);

                    // duplicate room details
                    $searched = RoomDetails::where(['room_id' => $details->id])->get();
                    foreach ($searched as $roomSearched) {
                       $internalSearched = RoomDetails::find($roomSearched->id);
                       $newRoomDetails = $internalSearched->replicate();
                       $newRoomDetails->room_id = $newlySaveApartment->id;
                       $newRoomDetails->save();
                    }

                    // duplicate room prices
                    $searchedDetails = RoomPrices::where(['room_id' => $details->id])->first();
                    $newDetails = $searchedDetails->replicate();
                    //$newDetails->property_id = $searchedProperty->id;
                    $newDetails->room_id = $newlySaveApartment->id;
                    $newDetails->save();
                 }
              break;
           }

           // retrun
          return ApiResponse::returnSuccessData($searchedProperty);
       }
    }


    public function store_old(Request $request)
    {
        if(!empty($request->getFields)) {
            return ApiResponse::returnData(Schema::getColumnListing('properties'));
        }
        else {
            // Validation
            $rules = [
                'name' => "required",
                'street_name' => "required",
                'city' => "required",
                'primary_telephone' => "required",
                'user_id' => "required",
                'email' => "required|email:filter"
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return ApiResponse::returnErrorMessage($message = $validator->errors());
            }

            // Pre-data Processing
            $request->request->add(['created_by' => $request->user_id]);

            // Saving Data
            if($property = Property::create($request->all()))
                return ApiResponse::returnSuccessMessage($message = "Property Saved and Awaiting Review.");
            else
                return ApiResponse::returnErrorMessage($message = "An Error Occurred. Please Try Again or Contact Support");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
       // validation
       $rules = [
          'id' => "required|exists:properties,uuid",
          'delete_id' => "required",
       ];
       $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
       if($validator->fails()) {
          return ApiResponse::returnErrorMessage($message = $validator->errors());
       }
       else {
          $searchedProperty = Property::where(['uuid' => $request->id])->first();
          switch ($searchedProperty->property_type_id) {
             case 1 :
                // apartment details
                //return $searchedDetails = ApartmentDetail::where(['uuid' => $request->delete_id])->first();
                if($searchedDetails = ApartmentDetail::where(['uuid' => $request->delete_id])->first())
                   $searchedDetails->update(['status' => DELETED_PROPERTY]);

                break;
          }

          return ApiResponse::returnSuccessMessage("Property Deleted Successfully");
       }
    }


   public function HotelDetails(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required",
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         # properties tbl
         $where_condition = ['uuid' => $request->id];
         $searchedProperty = Property::with('hoteldetails', 'OtherHotelDetails')->where($where_condition)->first();
         # return
         return ApiResponse::returnSuccessData(@$searchedProperty);
      }
   }
}
