<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if(!empty($request->getFields)) {
            return ApiResponse::returnData(Schema::getColumnListing('properties'));
        }
        else {
            // Validation
            $rules = [
                'name' => "required",
                'street_name' => "required",
                'city' => "required",
                'primary_telephone' => "required"
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return ApiResponse::returnErrorMessage($message = $validator->errors());
            }

            // Pre-data Processing

            // Saving Data
            if($property = Property::create($request->all()))
                return ApiResponse::returnSuccessMessage($message = "Property Saved and Awaiting Review.");
            else
                return ApiResponse::returnErrorMessage($message = "An Error Occurred. Please Try Again or Contact Support");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
