<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\CommonPropertyFacility;
use App\Models\CommonRoomAmenities;
use App\Models\Country;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyRating;
use App\Models\ApartmentDetail;
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
         if($searchedPropertys = Property::where(['created_by' => $request->userid])->get()) {
            foreach ($searchedPropertys as $property) {
               # other searches
               $apartmentDetails = ApartmentDetail::where(['property_id' => $property->id])->first();

               $responseData[] = [
                  'uuid' => $property->uuid,
                  'property_type_text' => $property->property_type_text,
                  'name' => $property->name,
                  'street_address_1' => $property->street_address_1,
                  'display_img' => @$apartmentDetails->image_pathss[0],
                  'num_of_guest' => @$apartmentDetails->total_guest_capacity,
                  'num_of_rooms' => @$apartmentDetails->num_of_rooms
               ];
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
         'country' => "required"
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
            $selectFields = trim(implode(',', array('a.id','uuid','name','geolocation','street_address_1','primary_telephone',
               'serve_breakfast','languages_spoken','image_paths','b.id as room_id','total_guest_capacity','total_bathrooms',
               'num_of_rooms','a.summary_text','a.about_us','a.status as property_status','a.current_onboard_stage'
            )), ',');
            $leftJoins = implode('', array('left join apartment_details b on b.property_id = a.id',
               //'left join common_room_amenities c on c.id = b.common_room_amenity_id'
            ));
            $query = "select {$selectFields} from properties a {$leftJoins} where ".$where_condition;
            $searchedPropertys = DB::select($query);

            foreach ($searchedPropertys as $property) {
               # variables
               $images = explode(STRING_GLUE, $property->image_paths);
               $geoData = explode(',', $property->geolocation);
               $roomPrices = RoomPrices::whereRaw("room_id = $property->room_id")->first();

               $guestOccupancy = explode(STRING_GLUE, $roomPrices->guest_occupancy);
               $allDiscounts = explode(STRING_GLUE, $roomPrices->discount);
               $allamount = explode(STRING_GLUE, $roomPrices->amount);
               $priceIndex = array_search($request->num_of_guests, $guestOccupancy);

               if($searchedFacilities = CommonPropertyFacility::where(['property_id' => $property->id])->first()) {
                  $facilities_ids = explode(STRING_GLUE, $searchedFacilities->facility_ids);
                  $facilities = Facility::select(['name','icon_class'])->find($facilities_ids);
               }
               if($searchedAmenities = CommonRoomAmenities::where(['room_id' => $property->room_id])->first()) {
                  $amenities_ids = explode(STRING_GLUE, $searchedAmenities->popular_amenity_ids);
                  $amenities = Amenity::select(['name','icon_class'])->find($amenities_ids);
               }

               $responseData[] = [
                  'uuid' => $property->uuid,
                  'current_onboard_stage' => $property->current_onboard_stage,
                  'status' => PROPERTY_STATUSES[$property->property_status],
                  'name' => $property->name,
                  'geolocation' => $property->geolocation,
                  'street_address_1' => $property->street_address_1,
                  'serve_breakfast' => $property->serve_breakfast,
                  'languages_spoken' => explode(STRING_GLUE, $property->languages_spoken),
                  'displayImg' => $images[0],
                  'distance_from_location' => ceil(PropertyListingController::distance($request->latitude,$request->longitude,@$geoData[0],$geoData[1],'K'))." km",
                  'price' => $allamount[$priceIndex],
                  'discount_given' => $allDiscounts[$priceIndex],
                  'facilities' => $facilities,
                  'amenities' => $amenities,
                  'rating' => PropertyRating::where(['property_id' => $property->id])->first()->current_rating ?? "Not Rated",
                  'summary_text' => $property->summary_text,
                  'about_us' => $property->about_us,
                  'room_details' => RoomDetails::where(['room_id'=>$property->room_id])->get()
               ];
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
}
