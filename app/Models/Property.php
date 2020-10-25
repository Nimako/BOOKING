<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','property_type_id','name','text_location','geolocation','street_address','postal_code',
       'country_id','about_us','summary_text','primary_telephone','secondary_telephone','email','website','nearby_locations',
       'serve_breakfast','languages_spoken','images_ids','images_paths','current_onboard_stage','created_by'];
}
