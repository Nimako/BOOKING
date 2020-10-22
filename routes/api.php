<?php

use App\Http\Controllers\DefaultValuesController;
use App\Http\Controllers\NewPropertyListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//INCIDENT
Route::group(['prefix'=>'auth'],function(){
     //Route::post('signup', 'App\Http\Controllers\AccountController@SignUp');
     Route::post('/SignupFirebase',  [AccountController::class,'SignUpFireBase']);
     Route::post('/SignupManual',  [AccountController::class,'SignUpManual']);
     Route::post('/SignInManual',  [AccountController::class,'SignInManual']);
     Route::post('/SendVerificationEmail',  [AccountController::class,'SendVerificationEmail']);
     Route::post('/VerifyUser',  [AccountController::class,'VerifyUser']);


     Route::post('/AuthHistory',  [AccountController::class,'SaveLoginHistory']);
});

// Default Item Listing
Route::group(['prefix'=>'default-items'],function(){
   Route::post('/',  [DefaultValuesController::class,'GetDefaultList']);
});

// Property Listing
Route::group(['prefix'=>'property'],function(){
    Route::post('/onboarding/stage1',  [NewPropertyListingController::class,'stage1']);
    Route::post('/onboarding/stage2',  [NewPropertyListingController::class,'stage2']);
    Route::post('/onboarding/stage3',  [NewPropertyListingController::class,'stage3']);
});



