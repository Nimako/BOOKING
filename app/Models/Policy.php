<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

   public function sub_policies()
   {
      return $this->hasMany('App\Models\SubPolicy');
   }

}
