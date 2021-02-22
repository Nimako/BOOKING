<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedType extends Model
{
    use HasFactory;

    protected $hidden = ['id','created_at','created_by','updated_at','updated_by','status'];

    protected $fillable = ['name','expected_sleeps','dimension'];
}
