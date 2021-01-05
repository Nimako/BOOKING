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
                        if(!empty($image)) {
                           $a = explode('storage/', $image);
                           $copy4rm = storage_path($a[1]);

                           $cpy_img = str_replace(' ', '_', substr($image, 0, strripos($image, '.'))."_dup_".microtime().".webp");
                           $a = explode('storage/', $cpy_img);
                           $copy2 = storage_path($a[1]);

                           File::copy($copy4rm, $copy2);
                           $imageReturned = explode('public/', $cpy_img);

                           return $imageReturned[1];
                        }

                     }, (array)$details->image_pathss);

                    // duplicate apartment details
                    //return $images;
                    $apartmentDetails = [
                       'uuid' => Uuid::uuid6(),
                       'property_id' => $searchedProperty->id,
                       'room_name' => $details->room_name,
                       'total_guest_capacity' => $details->total_guest_capacity,
                       'total_bathrooms' => $details->total_bathrooms,
                       'num_of_rooms' => $details->num_of_rooms,
                       //'common_room_amenity_id' => $details->common_room_amenity_id,
                       'image_paths' => implode(STRING_GLUE, $images),
                       'price_list' => json_encode($details->price_list)
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

    public function Reservation(Request $request)
    {

    }
}
