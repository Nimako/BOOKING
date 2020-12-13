<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPartnerAccount extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'password', 'fullname', 'phone_no', 'useraccount_id', 'email_token', 'uuid', 'token_expiration', 'token_validated'];
    protected $hidden = ['created_at', 'updated_at'];
}
