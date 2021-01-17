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
               $explodedImages = explode(STRING_GLUE, $searchedDetails->image_paths);
               $images = array_map(function($image){
                  if(!empty($image)) {
                     $a = explode('storage/', $image);
                     $copy4rm = storage_path('app\\public\\'.$a[0]);

                     $cpy_img = str_replace(' ', '_', substr($image, 0, strripos($image, '.'))."_dup_".microtime().".webp");
                     $a = explode('storage/', $cpy_img);
                     $copy2 = storage_path('app\\public\\'.$a[0]);

                     File::copy($copy4rm, $copy2);
                     $imageReturned = explode('public/', $cpy_img);

                     return $imageReturned[0];
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
