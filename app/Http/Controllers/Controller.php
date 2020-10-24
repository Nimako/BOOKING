<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

/***********************************************
Status For Processing Data
/************************************************/
define('AWAITING_APPROVAL', 0);
define('VALIDATION_ERROR', 400);
define('SUCCESS', 200);
define('ERROR', 300);
