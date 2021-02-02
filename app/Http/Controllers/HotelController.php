<?php

namespace App\Http\Controllers;

use App\Models\HotelDetails;
use App\Models\Property;
use App\Services\SharedPropertyService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class HotelController extends Controller
{
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

   public function DuplicateRoomDetails(Request $request)
   {
      $rules = [
         'id' => "required|exists:properties,uuid",
         'duplicate_id' => "required"
      ];
      $validator = Validator::make($request->all(), $rules);

      if($validator->fails())
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      else
         return ApiResponse::returnSuccessData((new SharedPropertyService())->duplicateProperty($request));
   }

   public function destroy(Request $request)
   {
      $rules = [
         'id' => "required|exists:properties,uuid",
         'delete_id' => "required",
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);

      if($validator->fails())
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      else
         return ApiResponse::returnSuccessMessage((new SharedPropertyService())->deleteProperty($request));
   }
}
