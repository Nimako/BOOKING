<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, Redirect, Response;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Illuminate\Support\Facades\DB;

class WebAuthController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth',["except"=>"index"]);
    }

    public function index()
    {
        return view('login');
    }


    public function registration()
    {
        if (Auth::check()) {

            $data['list'] = AdminUser::where('Status', '<>', "DELETED")->get();
            $data['showroom'] = DB::table("showroom")->get();

            return view('registration',$data);
        }
        return Redirect::to("login")->withSuccess('Opps! You do not have access');
    }
    

    public function postLogin(Request $request)
    {
        request()->validate([
            'username'  => 'required',
            'password'  => 'required',
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
         
            return Redirect::to("dashboard");
        }

        return Redirect::to("login")->withSuccess('Oppes! You have entered invalid credentials');
    }


    public function postRegistration(Request $request)
    {
       request()->validate([
            'FullName'     => 'required',
           // 'email'        => 'email|unique:employee',
            'username'     => 'required|unique:employee',
            'password'     => 'required|min:5',
            'UserType'     => 'required',
            'ShowRoomID'   => 'required',
        ]);
        $data = $request->all();

        $result = $this->create($data);

        return Redirect::to("registration")->withSuccess('Great! Account created Successfully');
    }


    public function dashboard()
    {
        if (Auth::check()){  
            
            $data['totalOrders']     = DB::table('transaction')->whereRaw("DATE_FORMAT(DateCreated, '%Y-%m-%d') = CURRENT_DATE()")->count();
            $data['totalOrders']     = DB::table('transaction')->whereRaw("DATE_FORMAT(DateCreated, '%Y-%m-%d') = CURRENT_DATE()")->count();
            $data['totalOrders']     = DB::table('transaction')->whereRaw("DATE_FORMAT(DateCreated, '%Y-%m-%d') = CURRENT_DATE()")->count();
            $data['totalOrders']     = DB::table('transaction')->whereRaw("DATE_FORMAT(DateCreated, '%Y-%m-%d') = CURRENT_DATE()")->count();

            $SessionShowRoomID = session('SessionShowRoomID');
            $ShowRoomID = !empty($SessionShowRoomID)?$SessionShowRoomID:Auth()->user()->ShowRoomID;

            $data['list'] = DB::table('transaction')->where("ShowRoomID",$ShowRoomID)->get();
            
            return view('dashboard',$data);
       }
        return Redirect::to("login")->withSuccess('Opps! You do not have access');
    }


    public function create(array $data)
    {
        return User::create([
            'FullName'           => $data['FullName'],
            'email'              => $data['email'],
            'username'           => $data['username'],
            'password'           => Hash::make($data['password']),
            'UserType'           => $data['UserType'],
            'ShowRoomID'         => $data['ShowRoomID'],
            'acumatica_username' => $data['acumatica_username'],
            'acumatica_password' => $data['acumatica_password'],
            'acumatica_SalespersonID' => $data['acumatica_SalespersonID']
        ]); 
    }

    public function editUser($id)
    {
        $data = [];
        $data['list'] = User::where('Status', '<>', "DELETED")->get();
        $data['showroom'] = DB::table("showroom")->get();

        $data['info'] = User::where(['id' => $id])->first();

        return view('registration', $data);
    }

    
    public function updateRegistration(Request $request)
    {
        $data = [
                'FullName'        => $request->FullName,
                'email'             => $request->email,
                'username'          => $request->username,
                'UserType'          => $request->UserType,
                'ShowRoomID'        => $request->ShowRoomID,
                'UserType'          => $request->UserType,
                'PasswordChanged'   => "NO",
                'acumatica_username' => $request->acumatica_username,
                'acumatica_password' => $request->acumatica_password,
                'acumatica_SalespersonID' => $request->acumatica_SalespersonID
        ];

        if(!empty($request->password)){
            $data['password'] = Hash::make($request->password);
        }

        $id =  $request->user_id;
        $affected = DB::table('employee')->where('id', $id)->update($data);

        if ($affected) {
            return Redirect::to("registration")->withSuccess('User Update Successfully');
        }else{
            return Redirect::to("registration");
        }
    }


    

    public function deleteRegistration($id){

        $affected = DB::table('employee')->where('id', $id)->update(['Status'=>'DELETED']);

        if ($affected) {
            return Redirect::to("registration");   
        }
    }



    public function logout()
    {
        Session::flush();
        Auth()->guard('admin')->logout();
        return Redirect('login');
    }



    public function SetShowRoomSession($ShowRoomID){

       session(['SessionShowRoomID' => $ShowRoomID]);
       return Redirect::back()->withErrors(['msg', 'The Message']);
       
    }



}
