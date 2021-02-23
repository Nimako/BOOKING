<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TestController;

use App\Http\Controllers\WebRegisteredUsersController;
use App\Http\Controllers\WebPropertyController;
use App\Http\Controllers\WebNewPropertyListingController;
use App\Http\Controllers\WebLookupSetupController;
use App\Http\Controllers\WebDashboardController;
use App\Http\Controllers\WebAuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WebAuthController::class, 'index']);

Route::get('/dashboard', [WebDashboardController::class, 'index']);

Route::get('login', [WebAuthController::class, 'index'])->name('login');
Route::post('post-login', [WebAuthController::class, 'postLogin']);
Route::get('registration', [WebAuthController::class, 'registration']);
Route::post('post-registration', [WebAuthController::class, 'postRegistration']);
Route::post('update-registration', [WebAuthController::class, 'updateRegistration']);
Route::get('delete-registration/{id}', [WebAuthController::class, 'deleteRegistration']);
//Route::get('dashboard', [AuthController::class, 'dashboard']);
Route::get('logout', [WebAuthController::class, 'logout']);

Route::get('/pending-properties', [WebPropertyController::class, 'pendingProperties']);
Route::get('/approved-properties', [WebPropertyController::class, 'approvedProperties']);
Route::get('/property-detail/{id}', [WebPropertyController::class, 'propertyDetail'])->name("propertyDetail");
Route::post('/change-status', [WebPropertyController::class, 'ChangePropertyStatus']);


// Property Listing
Route::group(['prefix'=>'lookupSetup'],function(){
     Route::get('/amenities',  [WebLookupSetupController::class,'amenities']);
     Route::post('/save-amenities',  [WebLookupSetupController::class,'SaveAmenities']);

     Route::get('/facilities',  [WebLookupSetupController::class,'facilities']);
     Route::post('/save-facilities',  [WebLookupSetupController::class,'SaveFacilities']);

     Route::get('/bedTypes',  [WebLookupSetupController::class,'betTypes']);
     Route::post('/save-bedTypes',  [WebLookupSetupController::class,'SaveBetTypes']);
});


Route::get('/partner-users',  [WebRegisteredUsersController::class,'partnerUser']);
Route::get('/guest-users',  [WebRegisteredUsersController::class,'GuestUser']);

Route::get('VerifyUser/{id}',  [AccountController::class,'VerifyUser'])->name("verifyaccount");
Route::get('DeleteUser/{email}',  [TestController::class,'DeleteUser']);
