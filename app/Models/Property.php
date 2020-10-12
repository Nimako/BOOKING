<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'about_us', 'primary_telephone', 'secondary_telephone', 'longitude_latitude',
        'logo_path', 'country_region', 'status', 'created_by', 'updated_by'];
}
