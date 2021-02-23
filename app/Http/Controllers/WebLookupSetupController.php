<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Models\Facility;
use App\Models\SubPolicy;
use App\Models\PropertyType;
use App\Models\BedType;
use App\Models\Policy;
use App\Models\RoomType;
use Validator, Redirect, Response;

class WebLookupSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    
    public function amenities(){

       $data['list'] = Amenity::all();
       return view("lookups/amenities",$data);
    }

    public function SaveAmenities(Request $request){

        $query =  Amenity::create([
            'name'        => $request->name,
            'icon_class'  => $request->icon_class,
            'category'    => $request->category
        ]);
        if($query){
                return Redirect::to("lookupSetup/amenities")->withSuccess('Amenities added Successfully');
        }else{
            return Redirect::to("lookupSetup/amenities")->withErrors("Error try again");
        }
    }


    public function facilities(){

        $data['list'] = Facility::all();
        return view("lookups/facilities",$data);
    }
 
    public function SaveFacilities(Request $request){
 
         $query =  Facility::create([
             'name'        => $request->name,
             'icon_class'  => $request->icon_class,
         ]);
         if($query){
             return Redirect::to("lookupSetup/facilities")->withSuccess('Amenities added Successfully');
         }else{
             return Redirect::to("lookupSetup/facilities")->withErrors("Error try again");
         }
    }


    public function betTypes(){

        $data['list'] = BedType::all();
        return view("lookups/bedTypes",$data);
    }
 
    public function SaveBetTypes(Request $request){
 
         $query =  BedType::create([
             'name'            => $request->name,
             'expected_sleeps' => $request->expected_sleeps,
             'dimension'       => $request->dimension,
         ]);
         if($query){
             return Redirect::to("lookupSetup/betTypes")->withSuccess('Bed type Successfully');
         }else{
             return Redirect::to("lookupSetup/betTypes")->withErrors("Error try again");
         }
    }



}
