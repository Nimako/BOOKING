<?php

use App\Http\Controllers\CronJobController;
use App\Http\Controllers\DefaultValuesController;
use App\Http\Controllers\NewPropertyListingController;
use App\Http\Controllers\PropertyListingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;


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


Route::group(['middleware' => 'checkheaders'], function () {

    Route::group(['prefix'=>'auth'],function(){
        //Route::post('signup', 'App\Http\Controllers\AccountController@SignUp');
        Route::post('/SignupFirebase',  [AccountController::class,'SignUpFireBase']);
        Route::post('/SignupManual',  [AccountController::class,'SignUpManual']);
        Route::post('/SignInManual',  [AccountController::class,'SignInManual']);
        Route::post('/SendVerificationEmail',  [AccountController::class,'SendVerificationEmail']);
        Route::post('/VerifyUser',  [AccountController::class,'VerifyUser']);

        Route::post('/ResetPassword',  [AccountController::class,'ResetPassword']);

        Route::post('/AuthHistory',  [AccountController::class,'SaveLoginHistory']);
    });
});


Route::group(['middleware' => 'api','prefix' => 'auths'], function ($router) {
    Route::post('/login',  [AuthController::class,'login']);
    // Route::post('login', 'AuthController@login');
    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    // Route::post('me', 'AuthController@me');
});



// Default Item Listing
Route::group(['prefix'=>'default-items'],function(){
   Route::post('/',  [DefaultValuesController::class,'GetDefaultList']);
});

// Property Listing
Route::group(['prefix'=>'property'],function(){
   # Onboarding routes
    Route::post('/onboarding/save',  [NewPropertyListingController::class,'onBoarding']);
    Route::post('/onboarding/search',  [NewPropertyListingController::class,'FullOnBoardingDetails']);
    Route::post('/onboarding/userproperties',  [PropertyListingController::class,'GetUserProperties']);

    Route::post('/onboarding/hotel/save',  [NewPropertyListingController::class,'HotelOnboarding']);

    # searching of approved properties
    Route::post('/search',  [PropertyListingController::class,'SearchProperty']);
    Route::post('/create-dummy',  [NewPropertyListingController::class,'CreateDummydata']);
});

// CronJob
Route::group(['prefix'=>'cronjob'],function(){
   Route::get('currency-rate',  [CronJobController::class,'CurrenyRateUpdate']);
});


// User Actions
Route::group(['prefix'=>'user'],function(){
   # Onboarding routes
   Route::post('/partner-account/save',  [UserController::class,'CreatePartnerAccount']);
   Route::post('verify-partner-account-token',  [UserController::class,'VerifyPartnerAccount']);
   Route::post('/login', [UserController::class,'login'])->name("login");

});

Route::post('/CompressImage',  [TestController::class,'CompressImage']);



