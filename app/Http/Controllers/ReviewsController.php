<?php

namespace App\Http\Controllers;

use App\Models\PropertyReview;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ReviewsController extends Controller
{


   public function SaveGuestReview(Request $request)
   {
      // validation
      $rules = [
         'property_id' =>  'required',
         'user_id'     =>  'required',
         'comment'     =>  'required'
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
                     $reviewSaveData = [
                        'uuid'        => Uuid::uuid6(),
                        'user_id'     => $request->user_id,
                        'property_id' => $request->property_id,
                        'comment'     => $request->comment,
                        'rating'      => $request->rating
                     ];
                     $responseData = PropertyReview::create($reviewSaveData);
                  }

         return ApiResponse::returnSuccessData($responseData);
   }


   public function OwnerReviewReply(Request $request)
   {
      // validation
      $rules = [
         'owner_id'       =>  'required',
         'owner_comment'  =>  'required',
         'review_id'      =>  'required',
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
               $reviewUpdateData = [
                  'owner_id'      => $request->owner_id,
                  'owner_comment' => $request->owner_comment,
               ];
               if($responseData = PropertyReview::where(['uuid' => $request->review_id])->update($reviewUpdateData))
                return ApiResponse::returnSuccessMessage("Reply successful");
               else
               return ApiResponse::returnErrorMessage("Failed to reply");
           }
   }


   public function GetPropertyReviews(Request $request)
   {
       $rules = [
         'property_id'    =>  'required',
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         if(!empty($request->status)){
            $ReviewData = PropertyReview::with("UserAccount")->where(['property_id' => $request->property_id,'status'=>$request->status])->get();
         }else{
            $ReviewData = PropertyReview::with("UserAccount")->where(['property_id' => $request->property_id])->get();
         }

         return ApiResponse::returnSuccessData($ReviewData);
      }
   }

   public function GetOwnerReviews(Request $request)
   {
       $rules = [
         'owner_id'    =>  'required',
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         if($ReviewData = PropertyReview::with("UserAccount")->where(['owner_id' => $request->owner_id,'status'=>1])->get())
         return ApiResponse::returnSuccessData($ReviewData);
         else
         return ApiResponse::returnErrorMessage("error");

      }
   }


   public function DeleteReview(Request $request)
   {
      // validation
      $rules = [
         'review_id'  =>  'required',

      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {           
              if(PropertyReview::where(['uuid' => $request->review_id])->update(['status'=>3]))
               return ApiResponse::returnSuccessMessage("comment deleted");
              else
              return ApiResponse::returnErrorMessage("Failed to delete review");
        }
   }  






   
}
