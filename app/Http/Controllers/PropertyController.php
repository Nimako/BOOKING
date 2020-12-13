<?php

namespace App\Http\Controllers;

use App\Models\ApartmentDetail;
use App\Models\CommonPropertyFacility;
use App\Models\CommonPropertyPolicy;
use App\Models\CommonRoomAmenities;
use App\Models\Property;
use App\Models\RoomDetails;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function DuplicateProperty(Request $request)
    {
       // validation
       $rules = [
          'id' => "required|exists:properties,uuid",
       ];
       $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
       if($validator->fails()) {
          return ApiResponse::returnErrorMessage($message = $validator->errors());
       }
       else {
           // property info
           $searchedProperty = Property::where(['uuid' => $request->id])->first();
           $newProperty = $searchedProperty->replicate();
           $newProperty->uuid = Uuid::uuid6();
           $newProperty->save();

           switch ($newProperty->property_type_id) {
              case 1 :
                 // common property facilities
                 $searchedDetails = CommonPropertyFacility::where(['property_id' => $searchedProperty->id])->first();
                 $newDetails = $searchedDetails->replicate();
                 $newDetails->property_id = $newProperty->id;
                 $newDetails->save();

                 // common property policies
                 $searchedDetails = CommonPropertyPolicy::where(['property_id' => $searchedProperty->id])->first();
                 $newDetails = $searchedDetails->replicate();
                 $newDetails->property_id = $newProperty->id;
                 $newDetails->save();

                 // apartment details
                 $searchedDetails = ApartmentDetail::where(['property_id' => $searchedProperty->id])->get();
                 foreach ($searchedDetails as $details) {
                    // duplicate apartment details
                    $apartmentDetails = [
                       'property_id' => $newProperty->id,
                       'room_name' => $details->room_name,
                       'total_guest_capacity' => $details->total_guest_capacity,
                       'total_bathrooms' => $details->total_bathrooms,
                       'num_of_rooms' => $details->num_of_rooms,
                       //'common_room_amenity_id' => $details->common_room_amenity_id,
                       'image_paths' => implode(STRING_GLUE, $details->image_pathss),
                    ];
                    $newlySaveApartment = ApartmentDetail::create($apartmentDetails);

                    // duplicating common room amenities
                    $searchedDetails = CommonRoomAmenities::find($details->common_room_amenity_id);
                    $newDetails = $searchedDetails->replicate();
                    $newDetails->property_id = $newProperty->id;
                    $newDetails->room_id = $newlySaveApartment->id;
                    $newDetails->save();

                    //updating apartment details
                    ApartmentDetail::find($newlySaveApartment->id)->update(['common_room_amenity_id' => $newDetails->id]);

                    // duplicate room details
                    $searched = RoomDetails::where(['room_id' => $newlySaveApartment->id])->get(['id']);
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
          return ApiResponse::returnSuccessData($newProperty);
       }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    public function PropertyApproval($propertyId)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       // validation
       $rules = [
          'id' => "required|exists:properties,uuid",
       ];
       $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
       if($validator->fails()) {
          return ApiResponse::returnErrorMessage($message = $validator->errors());
       }
       else {
          $searchedProperty = Property::where(['uuid' => $request->id])->first();
          $searchedProperty->status = DELETED_PROPERTY;
          $searchedProperty->save();

          return ApiResponse::returnSuccessMessage("Property Deleted Successfully");
       }
    }
}
