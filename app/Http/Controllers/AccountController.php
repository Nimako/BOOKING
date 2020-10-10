<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;
use App\Models\UserAccount;



class AccountController extends Controller
{

    public function SignUpFireBase(Request $request)
    {
     
        if(empty($request->Email)){

            return response()->json([ 'statusCode' => 500, 'message' => "Email is required"]);

        }else{

        $query =  UserAccount::updateOrCreate(
                ['Email' => trim($request->Email),],
                [ 
                'FirstName'       => $request->FirstName,
                'LastName'        => $request->LastName,
                'DisplayName'     => $request->DisplayName,
                'DateBirth'       => $request->DateBirth,
                'PhoneNum'        => $request->PhoneNum,
                'Country'         => $request->Country,
                'City'            => $request->City,
                'Region'          => $request->Region,
                'ProfileImage'    => $request->ProfileImage,
                'Provider'        => $request->Provider,
                'FireBaseUserID'  => $request->FireBaseUserID,
                ]
            );
                                                                                                                                                                                                                                             
            if($query) {
                return response()->json(['statusCode' => 200,  "message" => "User save"]);
            }else{
                return response()->json(['statusCode' => 500, "message" => "Failed Saving"]);
            } 
           }
		
	
    }



    public function SignUpManual(Request $request)
    {
      
        #Assignment of request variables
        $UserAccount = new UserAccount();

        # Checking for duplication
        $duplicateEmail    = UserAccount::where('Email',$request->Email)->first();

        if(!empty($duplicateEmail)){

            return response()->json(['statusCode' => 500, 'message' => "Email Already Exist"]);

        }else{ 

            #Variable Assignments
            $UserAccount->Email        = $request->Email;
            $UserAccount->FirstName    = $request->FirstName;
            $UserAccount->LastName     = $request->LastName;
            $UserAccount->UserPassword = Hash::make($request->Password);
            $UserAccount->Provider     = 'manual';
            $UserAccount->Country      = $request->Country;
            $UserAccount->City         = $request->City;
            $UserAccount->Region       = $request->Region;
        
            if($UserAccount->save()) {

                $this->SendVerificationEmail($request);
                
                return response()->json(['statusCode' => 200,  "message" => "User save"]);

            }else{
                return response()->json(['statusCode' => 500, "message" => "Failed Saving"]);
            } 
        }

    }


    public function SignInManual(Request $request)
    {

        $user  = UserAccount::where('Email',$request->Email)->first();

        if(empty($request->Email) || empty($request->Password)){
            return response()->json(['statusCode' => 500,  "message" => "Email and Password is required"]);
        }

        if(empty($request->Provider) && $user->Provider !="manual"){
            return response()->json(['statusCode' => 500,  "message" => "Please use {$user->Provider} to login"]);
        }
        
        if(!empty($user)){

            if (Hash::check($request->Password, $user->UserPassword)){

                $data = [];

                $data['FirstName']     = $user->FirstName;
                $data['LastName']      = $user->LastName;
                $data['DisplayName']   = $user->DisplayName;
                $data['DateBirth']     = $user->DateBirth;
                $data['Country']       = $user->Country;
                $data['City']          = $user->City;
                $data['Region']        = $user->Region;
                $data['ProfileImage']  = $user->ProfileImage;
                $data['EmailVerify']  =  $user->EmailVerify;

                return response()->json(['payload'=>$data,'statusCode' => 200,  "message" => "Success"]);

            }else{
                return response()->json(['statusCode' => 500, "message" => "Invalid password combination"]);
            }


        }else{
               return response()->json(['statusCode' => 500, "message" => "Password is not found in our system"]);
        } 
    }




    public function SaveLoginHistory(Request $request)
    {

        $LoginHistory = new LoginHistory();

        # Checking if email exist
        $user    = UserAccount::where('Email',$request->Email)->first();

        if(!empty($user->id)){
 

            #Variable Assignments
            $LoginHistory->UserID          = $user->id;
            $LoginHistory->Provider        = $request->Provider;
            $LoginHistory->LoginToken      = $request->LoginToken;
            $LoginHistory->UserAgentData   = $request->UserAgentData;
            $LoginHistory->IPAddress       = $request->IPAddress;
            $LoginHistory->Latitude        = $request->Latitude;
            $LoginHistory->Longitude       = $request->Longitude;
            $LoginHistory->CountryShort    = $request->CountryShort;
            $LoginHistory->CountryFull     = $request->CountryFull;
            $LoginHistory->City            = $request->City;
            $LoginHistory->Region          = $request->Region;
            $LoginHistory->Timezone        = $request->Timezone;
            $LoginHistory->BrowserType     = $request->BrowserType;
            $LoginHistory->DeviceType      = $request->DeviceType;
            $LoginHistory->BrowserType     = $request->BrowserType;

            if($LoginHistory->save()) {
 
                UserAccount::where('Email',$request->Email)->update(['LastLogin' => Carbon::now()]);

                return response()->json(['statusCode' => 200,  "message" => "Login History Log"]);
            }else{
                return response()->json(['statusCode' => 500, "message" => "Failed Saving"]);
            } 
        }

    }




    public function SendVerificationEmail(Request $request){

      $data   = UserAccount::where('Email',trim($request->Email))->first();

      $user = [];
      $user['token'] = sha1(time().$request->Email.mt_rand(00000,99999));
      $user['email'] = $data->Email;
      $user['name']  = $data->FirstName ." ". $data->LastName;

        $affected = DB::table('verifyuser')->insert(
            ['UserID' => $data->id, 'token' => $user['token'], 'DateCreated'=> date('Y-m-d')]
        );

        if($affected){

            Mail::to(trim($request->Email))->send(new VerifyMail($user));

            return response()->json(['statusCode' => 200,  "message" => "Verification link sent"]);

        }

    }


    public function VerifyUser(Request $request,$token){

        //$verifyUser      = DB::table('VerifyUser')->where('token',$request->token)->first();
        $verifyUser      = DB::table('verifyuser')->where('token',$token)->first();

        if(!empty($verifyUser)){ 

            $user   = UserAccount::where('id',$verifyUser->UserID)->first();

            if($user->Verified=="YES"){
                return response()->json(['statusCode' => 200,  "message" => "Your e-mail is already verified. You can now login"]);
            }


            $today  = date("Y-m-d");
            $expire = $verifyUser->DateCreated; 
            
            $today_time  = strtotime($today);
            $expire_time = strtotime($expire);

            if ($expire_time < $today_time){
                return response()->json(['statusCode' => 200,  "message" => "Sorry your verification link has expired"]);
            }else{

                $affected = DB::table('useraccount')->where('id', $verifyUser->UserID)->update(['Verified' => "YES"]);

                return response()->json(['statusCode' => 200,  "message" => "Your e-mail is verified. You can now login"]);
            }   
        }else{
            return response()->json(['statusCode' => 200,  "message" => "Sorry your verification link has expired"]);
        }

    }






}


