<?php

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
  


