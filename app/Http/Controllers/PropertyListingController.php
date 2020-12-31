<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\CommonPropertyFacility;
use App\Models\CommonRoomAmenities;
use App\Models\Country;
use App\Models\Facility;
use App\Models\HotelDetails;
use App\Models\Property;
use App\Models\PropertyRating;
use App\Models\ApartmentDetail;
use App\Models\PropertyType;
use App\Models\RoomDetails;
use App\Models\RoomPrices;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyListingController extends Controller
{
   public function GetUserProperties(Request $request)
   {
      // Validation
      $rules = [
         'userid' => "required|exists:useraccount,id"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['userid.exists' => "Invalid User Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else{
         # variable declaration
         $responseData = array();
         if($searchedPropertys = Property::where(['created_by' => $request->userid])->whereRaw('status != '.DELETED_PROPERTY)->get()) {
            foreach ($searchedPropertys as $property) { //return $property;
               # other searches
               switch ($property->property_type_id) {
                  case 1:
                     $apartmentDetails = ApartmentDetail::where(['property_id' => $property->id])->first();
                     $totalGuestCapacity = $apartmentDetails->total_guest_capacity;
                     $images = $apartmentDetails->image_pathss;
                     break;

                  case 3 :
                     $hotelDetails = HotelDetails::where(['property_id' => $property->id])->first();
                     $totalGuestCapacity = $hotelDetails->total_guest_capacity;
                     $images = explode(STRING_GLUE, $hotelDetails->image_paths);
                     break;
               }


               $responseData[] = [
                  'uuid' => $property->uuid,
                  'property_type_text' => $property->property_type_text,
                  'name' => $property->name,
                  'street_address_1' => $property->street_address_1,
                  'display_img' => @$images[0],
                  'num_of_guest' => @$totalGuestCapacity,
                  'num_of_rooms' => @$apartmentDetails->num_of_rooms
               ];

               ## resetting values
               unset($images, $totalGuestCapacity);
            }
         }

         # return
         return ApiResponse::returnSuccessData($responseData);
      }
   }

   public function SearchProperty(Request $request)
   {
      // Validation
      $rules = [
         'country' => "required",
         'city' => "required",
         'latitude' => "required",
         'longitude' => "required",
         'property_type' => "required",
      ];
      $validator = Validator::make($request->all(), $rules);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         #
         if($countryDetails = Country::where(['iso' => $request->country])->first()) {
            # variable conditions
            $country_city_condition = " and a.country_id =  ".$countryDetails->id." and a.city like '%".$request->city."%' ";
            if($request->property_type)
               $property_type_condition = " and a.property_type_id = ".$request->property_type;
            if($request->num_of_guests)
               $numofguest_condition = " and b.total_guest_capacity >= ".$request->num_of_guests;
            if($request->num_of_rooms)
               $numofrooms_condition = " and b.num_of_rooms >= ".$request->num_of_rooms;

            # where condition
            $where_condition = "a.status = ".PUBLISHED_PROPERTY.$country_city_condition.
               @$property_type_condition.
               @$numofguest_condition.
               @$numofrooms_condition;

            # query build and result
            $selectFields = trim(implode(',', array('a.id','a.uuid','name','geolocation','street_address_1','primary_telephone',
               'serve_breakfast','languages_spoken','image_paths','b.id as room_id','total_guest_capacity','total_bathrooms',
               'num_of_rooms','a.summary_text','a.about_us','a.status as property_status','a.current_onboard_stage',
               '(select name from property_types where property_types.id = a.property_type_id) as property_type_text '
            )), ',');
            $leftJoins = implode(' ', array('left join apartment_details b on b.property_id = a.id',
               //'left join common_room_amenities c on c.id = b.common_room_amenity_id'
            ));
            $distinctQuery = "select distinct a.id from properties a {$leftJoins} where ".$where_condition;
            $propertiesFound = DB::select($distinctQuery);
            if(!empty($propertiesFound))
            {
               $property_ids = implode(',', array_map(function($data){ return $data->id;}, $propertiesFound));
               $query = "select {$selectFields} from properties a {$leftJoins} where a.id in ({$property_ids})";
               $searchedPropertys = DB::select($query);

               // removing duplicates
               foreach ($searchedPropertys as $property) { $formatted[$property->id] = $property; }
               foreach ($formatted as $property) {
                  # variables
                  $images = explode(STRING_GLUE, $property->image_paths);
                  $geoData = explode(',', $property->geolocation);
                  $roomPrices[] = RoomPrices::whereRaw("room_id = $property->room_id")->first();

                  $guestOccupancy = explode(STRING_GLUE, @$roomPrices->guest_occupancy);
                  $allDiscounts = explode(STRING_GLUE, @$roomPrices->discount);
                  $allamount = explode(STRING_GLUE, @$roomPrices->amount);
                  $priceIndex = array_search($request->num_of_guests, $guestOccupancy);

                  if($searchedFacilities = CommonPropertyFacility::where(['property_id' => $property->id])->first()) {
                     $facilities_ids = explode(STRING_GLUE, $searchedFacilities->facility_ids);
                     $facilities = Facility::select(['name','icon_class'])->find($facilities_ids);
                  }
                  if($searchedAmenities = CommonRoomAmenities::where(['room_id' => $property->room_id])->first()) {
                     $amenities_ids = explode(STRING_GLUE, $searchedAmenities->popular_amenity_ids);
                     $amenities = Amenity::select(['name','icon_class'])->find($amenities_ids);
                  }


                  $distanceFromLocation = ceil(PropertyListingController::distance($request->latitude,$request->longitude,@$geoData[0],@$geoData[1],'K'))." km";
                  $responseData[] = [
                     'uuid' => $property->uuid,
                     'current_onboard_stage' => $property->current_onboard_stage,
                     'status' => PROPERTY_STATUSES[$property->property_status],
                     'name' => $property->name,
                     'property_type_text' => $property->property_type_text,
                     'geolocation' => $property->geolocation,
                     'street_address_1' => $property->street_address_1,
                     'serve_breakfast' => $property->serve_breakfast,
                     'languages_spoken' => explode(STRING_GLUE, $property->languages_spoken),
                     'displayImg' => $images[0],
                     'distance_from_location' => $distanceFromLocation,
                     'price' => $allamount[$priceIndex],
                     'discount_given' => $allDiscounts[$priceIndex],
                     'facilities' => $facilities,
                     'amenities' => @$amenities,
                     'rating' => PropertyRating::where(['property_id' => $property->id])->first()->current_rating ?? "Not Rated",
                     'summary_text' => $property->summary_text,
                     'about_us' => $property->about_us,
                     'room_details' => RoomDetails::where(['room_id'=>$property->room_id])->get()
                  ];
               }
            }

         }

         # return
         return ApiResponse::returnSuccessData(@$responseData);
      }
   }

   public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
         return ($miles * 1.609344);
      } else if ($unit == "N") {
         return ($miles * 0.8684);
      } else {
         return $miles;
      }
   }

   public function booking(Request $request)
   {
      // Validation
      $rules = [
         'userid' => "required",
         'parent_property' => "required|exists:properties,uuid"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['parent_property.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         if(Booking::insert($request->all()))
            return ApiResponse::returnSuccessMessage($message = "Property Booked");
         else
            return ApiResponse::returnErrorMessage($message = "An Error Occurred");
      }
   }
}
