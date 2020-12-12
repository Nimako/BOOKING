<?php

namespace App\Http\Controllers;

use App\Events\SendEmail;
use App\Models\UserPartnerAccount;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
   public function CreatePartnerAccount(Request $request)
   {
      // Validation
      $rules = [
         'email' => "required",
         'fullname' => "required",
         'password' => 'min:8|required_with:confirm_password|same:confirm_password',
         'confirm_password' => 'min:8'
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         // validating email
         if(UserPartnerAccount::where(['email' => $request->email])->first())
            return ApiResponse::returnErrorMessage($message = "Email Already Exists");
         // adding new request params
         $email_token = sha1(microtime().$request->email);
         $request->merge(['uuid' => Uuid::uuid6(), 'email_token' => $email_token]);
         if($responseData = UserPartnerAccount::create($request->except('confirm_password'))){
            // Sending Email
            $details = [
               'email' => $request->email,
               'token' => $email_token,
            ];
            event(new SendEmail($request->email, $details));

            // return
            return ApiResponse::returnSuccessData($responseData);
         }
         else
            return ApiResponse::returnErrorMessage($message = "An Error Occurred");
      }
    }

   public function VerifyPartnerAccount(Request $request)
   {
      $rules = [
         ''
      ];
    }
}
