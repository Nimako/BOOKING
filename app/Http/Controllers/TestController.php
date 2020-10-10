<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    
    public function DeleteUser($email){

      //$query =  DB::table('useraccount')->delete();

      $query = DB::table('users')->where('Email', $email)->delete();


      if($query){
          return "all user accounts deleted";
      }else{
        return "Try again";

      }

    }
}
