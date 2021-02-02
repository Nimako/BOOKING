<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\BedType;
use App\Models\Country;
use App\Models\Facility;
use App\Models\Policy;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DefaultValuesController extends Controller
{
   public function GetDefaultList(Request $request)
   {
      switch ($request->item) {
         case 'amenities' :
            $responseData = Amenity::all(['id','name','icon_class']);
            break;
         case 'facilities' :
            $responseData = Facility::all(['id','name','icon_class']);
            break;
         case 'policies' :
            $responseData = Policy::with('sub_policies')->get(['id','name']);
            break;
         case 'property_types' :
            $responseData = PropertyType::all(['uuid','name','description']);
            break;
         case 'country' :
            $responseData = Country::all(['id','iso','name','currency']);
            break;
         case 'bedtypes' :
            $responseData = BedType::all(['id','name']);
            break;
         case 'roomtypes' :
            $retrieved = RoomType::distinct()->get(['category'])->toArray();
            $responseData = array_map(function($data){ return $data['category']; }, $retrieved);
            break;
         default :
            $responseData['options'] = ['amenities','facilities','policies','property_types','country','bedtypes','roomtypes'];
            break;
      }

      return ApiResponse::returnRawData($responseData);
   }

   public function GetRoomNames(Request $request)
   {
      $rules = [
         'room_category' => "required"
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         return ApiResponse::returnSuccessData(RoomType::where(['category' => $request->room_category])->get());
      }
   }
}
