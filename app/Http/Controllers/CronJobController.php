<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CronJobController extends Controller
{
    //
   public function CurrenyRateUpdate()
   {
      // api to hit
      return $response = Http::get('https://api.exchangeratesapi.io/latest?symbol=GHS,USD');
   }
}
