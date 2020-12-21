<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
       $credentials = request(['email', 'password']);
       if(empty($credentials['email'])){
          return response()->json(['statusCode'=>500, 'message' => 'Username is required']);
       }
       if(empty($credentials['password'])){
          return response()->json(['statusCode'=>500, 'message' => 'Password is required']);
       }
       $user = User::where("email",$credentials['email'])->first();

       if(empty($user)){
        return response()->json(['statusCode'=>500, 'message' => 'Email does not exist in our system']);
       }
       
       $customClaims = [
          "partner_id" => $user->id,
          "uuid"       => $user->uuid,
          "email"      => $user->email,
          "useraccount_id" => $user->useraccount_id,
          "phone_no"    => $user->phone_no,
          "role"        => "partner"
       ];
       try {
          if (! $token = JWTAuth::claims($customClaims)->attempt($credentials)) {
             return response()->json(['statusCode'=>500, 'message' => 'invalid username and password']);
          }
       } catch (JWTException $e) {
          return response()->json(['statusCode'=>500, 'message' => 'could_not_create_token']);
       }
       return $this->respondWithToken($token);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([

            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>  auth()->factory()->getTTL() * 60,
            "statusCode" =>  200,
            "message"    => "success"
        ]);
    }
}