<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $table       = 'loginhistory';

    protected $primaryKey  = 'id';

    const CREATED_AT       = 'SignInDate';
    const UPDATED_AT       = null;
}
