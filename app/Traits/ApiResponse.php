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
		return response()->json(['errors' => $errors],  Response::HTTP_BAD_REQUEST);
   }

   public static function returnSuccessData($data)
   {
      return response()->json(['data' => $data], Response::HTTP_CREATED);
   }

    public static function returnData($data)
    {
        return response()->json(['data' => $data], Response::HTTP_OK);
    }

   public static function returnSuccessMessage($message)
   {
      return response()->json(['message' => $message], Response::HTTP_OK);
   }

   public static function returnErrorMessage($message)
   {
      return response()->json(['message' => $message], Response::HTTP_NOT_ACCEPTABLE);
   }
}
