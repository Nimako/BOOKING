<?php

namespace App\Http\Controllers;

use App\Models\HotelDetails;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class HotelController extends Controller
{
   public function DuplicateRoomDetails(Request $request)
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

         switch ($searchedProperty->property_type_id) {
            case HOTELS :
               // hotel details
               $searchedDetails = HotelDetails::where(['uuid' => $request->duplicate_id])->first();
               // image duplication
               $explodedImages = $searchedDetails->image_paths;
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

               }, $explodedImages);
               // duplicate room
               $newRoomDetails = $searchedDetails->replicate();
               $newRoomDetails->uuid = Uuid::uuid6();
               $newRoomDetails->image_paths = implode(STRING_GLUE, $images);
               $newRoomDetails->save();
               break;
         }

         // retrun
         return ApiResponse::returnSuccessData($searchedProperty);
      }
    }

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
            case HOTELS :
               // apartment details
               if($searchedDetails = HotelDetails::where(['uuid' => $request->delete_id])->first()) {
                  $searchedDetails->status = DELETED_PROPERTY;
                  $searchedDetails->save();
               }
               break;
         }

         return ApiResponse::returnSuccessMessage("Property Deleted Successfully");
      }
   }
}
