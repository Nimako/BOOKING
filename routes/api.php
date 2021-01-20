<?php

use App\Http\Controllers\CronJobController;
use App\Http\Controllers\DefaultValuesController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\NewPropertyListingController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyListingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewsController;


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
    Route::post('refresh',[AuthController::class,'refresh']);
     Route::post('/me',[AuthController::class,'me']);


});



// Default Item Listing
Route::group(['prefix'=>'default-items'],function(){
   Route::post('/',  [DefaultValuesController::class,'GetDefaultList']);
   Route::post('/roomnames',  [DefaultValuesController::class,'GetRoomNames']);
});

// Property Listing
Route::group(['prefix'=>'property'],function(){
   # Onboarding routes
    Route::post('/onboarding/save',  [NewPropertyListingController::class,'onBoarding']);
    Route::post('/onboarding/search',  [NewPropertyListingController::class,'FullOnBoardingDetails']);
    Route::post('/onboarding/userproperties',  [PropertyListingController::class,'GetUserProperties']);



    # searching of approved properties
    Route::post('/search',  [PropertyListingController::class,'SearchProperty']);
    Route::post('/create-dummy',  [NewPropertyListingController::class,'CreateDummydata']);

    Route::post('/duplicate',  [PropertyController::class,'DuplicateProperty']);
    Route::post('/delete',  [PropertyController::class,'destroy']);

    # hotel routes
    Route::post('/hotel/view',  [PropertyController::class,'HotelDetails']);
    Route::post('/hotel/duplicate',  [HotelController::class,'DuplicateRoomDetails']);
    Route::post('/onboarding/hotel/save',  [NewPropertyListingController::class,'HotelOnboarding']);
    Route::post('/onboarding/hotel/duplicate',  [NewPropertyListingController::class,'HotelOnboarding']);
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
   Route::post('/change-password', [UserController::class,'ChangePassword']);
});



// Booking Apis
Route::group(['prefix'=>'reservation'],function(){
   # Onboarding routes
   Route::post('/save',  [ReservationController::class,'Reservation']);
   Route::post('/reschedule',  [ReservationController::class,'Reschedule']);
});


// Reviews Apis
Route::group(['prefix'=>'review'],function(){
   # Onboarding routes
   Route::post('/save',  [ReviewsController::class,'SaveGuestReview']);
   Route::post('/reply',  [ReviewsController::class,'OwnerReviewReply']);
   Route::post('/get-property-reviews',  [ReviewsController::class,'GetPropertyReviews']);
   Route::post('/get-owner-reviews',  [ReviewsController::class,'GetOwnerReviews']);
   Route::post('/delete-review',  [ReviewsController::class,'DeleteReview']);

});


Route::post('/CompressImage',  [TestController::class,'CompressImage']);



