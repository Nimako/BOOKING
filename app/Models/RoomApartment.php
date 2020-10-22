<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomApartment extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','num_of_rooms'];
}
