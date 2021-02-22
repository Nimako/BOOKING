<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPartnerAccount;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class WebRegisteredUsersController extends Controller
{


    public function index(){

         
    }


    public function GuestUser(){

        $data['list'] = DB::table("useraccount")->get();
        return view("users/GuestUsers",$data);
    }
    

    public function partnerUser(){

        $data['list'] = DB::table("user_partner_accounts")->get();
        return view("users/PartnerUsers",$data);
    }

}
