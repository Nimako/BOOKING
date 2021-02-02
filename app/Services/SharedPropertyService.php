<?php


namespace App\Services;


use App\Models\ApartmentDetail;
use App\Models\CommonRoomAmenities;
use App\Models\HotelDetails;
use App\Models\Property;
use App\Models\RoomDetails;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;

class SharedPropertyService
{
   public function duplicateProperty(Request $request)
   {
      $searchedProperty = Property::where(['uuid' => $request->id])->first();
      switch ($searchedProperty->property_type_id) {
         case APARTMENT :
            // apartment details
            $searchedDetails = ApartmentDetail::where(['uuid' => $request->duplicate_id])->first();
            $newApartmentDetails = $searchedDetails->replicate();
            $newApartmentDetails->uuid = Uuid::uuid6();
            $newApartmentDetails->image_paths = implode(STRING_GLUE, SharedPropertyService::duplicateImages($searchedDetails->image_pathss));
            $newApartmentDetails->save();

            // duplicating common room amenities
            $searchedDetails = CommonRoomAmenities::find($searchedDetails->common_room_amenity_id);
            $newDetails = $searchedDetails->replicate();
            $newDetails->property_id = $searchedProperty->id;
            $newDetails->room_id = $newApartmentDetails->id;
            $newDetails->save();

            //updating apartment details
            ApartmentDetail::find($newApartmentDetails->id)->update(['common_room_amenity_id' => $newDetails->id]);

            // duplicate room details
            $searched = RoomDetails::where(['room_id' => $newApartmentDetails->id])->get();
            foreach ($searched as $roomSearched) {
               $internalSearched = RoomDetails::find($roomSearched->id);
               $newRoomDetails = $internalSearched->replicate();
               $newRoomDetails->room_id = $newApartmentDetails->id;
               $newRoomDetails->save();
            }
         break;

         case HOTELS :
            $searchedDetails = HotelDetails::where(['uuid' => $request->duplicate_id])->first();
            // duplicate room
            $newRoomDetails = $searchedDetails->replicate();
            $newRoomDetails->uuid = Uuid::uuid6();
            $newRoomDetails->image_paths = implode(STRING_GLUE, SharedPropertyService::duplicateImages($searchedDetails->image_paths));
            $newRoomDetails->save();
         break;
      }

      return ApiResponse::returnRawData(HotelDetails::find($newRoomDetails->id));
   }

   public static function duplicateImages(Array $paths) : Array
   {
      $images = array_map(function($image){
         if(!empty($image)) {
            $a = explode('storage/', $image);
            $copy4rm = storage_path("/app/public/".$a[1]);

            $cpy_img = str_replace(' ', '_', substr($image, 0, strripos($image, '.'))."_dup_".microtime().".webp");
            $a = explode('storage/', $cpy_img);
            $copy2 = storage_path("/app/public/".$a[1]);

            File::copy($copy4rm, $copy2);
            $imageReturned = explode('storage/', $cpy_img);

            return $imageReturned[1];
         }

      }, $paths);

      return $images;
   }

   public function deleteProperty(Request $request)
   {
      $searchedProperty = Property::where(['uuid' => $request->id])->first();
      switch ($searchedProperty->property_type_id) {
         case APARTMENT :
           ApartmentDetail::where(['uuid' => $request->delete_id])->update(['status' => DELETED_PROPERTY]);
            break;

         case HOTELS :
            HotelDetails::where(['uuid' => $request->delete_id])->update(['status' => DELETED_PROPERTY]);
            break;
      }

      return $message = "Property Deleted Successfully";
   }
}
