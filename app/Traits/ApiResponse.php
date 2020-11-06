<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
   /**
    *  Request Validation Errors Api-response
    *
    * @param $errors
    * @return \Illuminate\Http\JsonResponse
    * @author OsborneMordreds
    */
   public static function returnValidationError($errors)
   {
		return response()->json(['statuscode' => VALIDATION_ERROR, 'errors' => $errors],  Response::HTTP_OK);
   }

   public static function returnSuccessData($data)
   {
      return response()->json(['statuscode' => SUCCESS, 'data' => $data], Response::HTTP_OK);
   }

    public static function returnData($data)
    {
        return response()->json(['statuscode' => SUCCESS,'data' => $data], Response::HTTP_OK);
    }

   public static function returnSuccessMessage($message)
   {
      return response()->json(['statuscode' => SUCCESS, 'message' => $message], Response::HTTP_OK);
   }

   public static function returnErrorMessage($message, $data = null)
   {
      return response()->json(['statuscode' => ERROR, 'message' => $message, 'details' =>$data], Response::HTTP_OK);
   }
}
