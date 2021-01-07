<?php

namespace App\Http\Controllers;

use App\Models\ApartmentDetail;
use App\Models\Booking;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ReservationController extends Controller
{
   public function Reservation(Request $request)
   {
      // validation
      $rules = [
         'id' => "required|exists:properties,uuid",
         'details' => "required",
         'booked_by' => "required",
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Property Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         $searchedProperty = Property::where(['uuid' => $request->id])->first();
         switch ($searchedProperty->property_type_id) {
            case APARTMENT:
               foreach ($request->details as $details){
                  if($searchedDetails = ApartmentDetail::where(['uuid' => $details['details_id']])->first()) {
                     $bookingSaveData = [
                        'uuid' => Uuid::uuid6(),
                        'booked_by' => $request->booked_by,
                        'property_id' => $searchedProperty->id,
                        'property_details_id' => $searchedDetails->id,
                        'expected_checkin' => $details['expected_checkin'],
                        'expected_checkout' => $details['expected_checkout'],
                        'num_of_rooms' => $details['num_of_rooms'],
                        'total_price' => $details['price'],
                        'promo_code' => @$details['promo_code'],
                        //'discount_applied' => $details['discount'],
                        'other_details' => json_encode(array(
                           'num_of_adults' => $details['num_of_adults'],
                           'num_of_children' => $details['num_of_children']
                        ))
                     ];
                     $responseData[] = Booking::create($bookingSaveData);
                  }
               }
               break;
         }

         return ApiResponse::returnSuccessData($responseData);
      }
   }

   public function Reschedule(Request $request)
   {
      // Validation
      $rules = [
         'id' => "required|exists:bookings,uuid"
      ];
      $validator = Validator::make($request->all(), $rules, $customMessage = ['id.exists' => "Invalid Booking Reference"]);
      if($validator->fails()) {
         return ApiResponse::returnErrorMessage($message = $validator->errors());
      }
      else {
         $searchedBooking = Booking::where(['uuid' => $request->id])->first();
         // new data
         $duplicate = $searchedBooking->replicate();
         (!empty($request->details[0]['expected_checkin'])) ? $duplicate->expected_checkin = $request->details[0]['expected_checkin'] : null;
         (!empty($request->details[0]['expected_checkout'])) ? $duplicate->expected_checkout = $request->details[0]['expected_checkout'] : null;
         (!empty($request->details[0]['num_of_rooms'])) ? $duplicate->num_of_rooms = $request->details[0]['num_of_rooms'] : null;
         (!empty($request->details[0]['price'])) ? $duplicate->total_price = $request->details[0]['price'] : null;
         $duplicate->save();
         // updating old
         $searchedBooking->update(['rescheduled' => YES, 'rescheduled_id' => $duplicate->id, 'status' => RESCHEDULED]);

         return ApiResponse::returnSuccessData($duplicate);
      }
   }
}
