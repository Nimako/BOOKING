<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    //

   public function ConfirmBooking(Request $request)
   {
      // validation
      $rules = [
         'id' => "required|exists:properties,uuid",
         'confirm_id' => "required",
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         $searchedProperty = Property::where(['uuid' => $request->id])->first();
         
      }
   }
}
