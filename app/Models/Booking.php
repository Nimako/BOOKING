<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','status','booked_by','property_id','property_details_id','expected_checkin','expected_checkout',
       'actual_checkin','actual_checkout','num_of_rooms','total_price','other_details','promo_code','discount_applied',
       'rescheduled','rescheduled_id'
    ];
}
