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
define('ERROR', 500);

##### Statuses #####
define('STATUS', array(
   'ACTIVE' => 1
));

# property status
define('PENDING_PROPERTY_APPROVAL', 2);
define('PROPERTY_IN_REVIEW', 3);
define('PUBLISHED_PROPERTY', 3);

#### Others ####
define('STRING_GLUE', '**');
