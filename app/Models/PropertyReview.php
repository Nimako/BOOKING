<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyReview extends Model
{
    use HasFactory;

    protected $fillable = ["uuid","status","property_id","comment","rating","owner_comment","owner_id","user_id"];

    protected $hidden   = ['created_at','updated_at'];

    public function UserAccount()
    {
       return $this->hasOne('App\Models\UserAccount','id','user_id')->where('status','<>', 3);

    }

}
