<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyListingController extends Controller
{
   public function GetUserProperties(Request $request)
   {
      // Validation
      $rules = [
         'userid' => "required|exists:useraccount,id"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['userid.exists' => "Invalid User Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         # variable declaration
         $responseData = array();
         if($searchedPropertys = Property::where(['created_by' => $request->userid])->get())
            $responseData = $searchedPropertys;

         # return
         return ApiResponse::returnSuccessData($responseData);
      }
   }
}
