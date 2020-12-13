<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPartnerAccount extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'password', 'fullname', 'phone_no', 'useraccount_id', 'email_token', 'uuid'];
    protected $hidden = ['created_at', 'updated_at'];

   public function setPasswordAttribute()
   {
      return password_hash($this->password, PASSWORD_DEFAULT);
   }

   


}
