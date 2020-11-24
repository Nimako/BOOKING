<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, $exception)
    {
        # Mysql Query Error
        /*if($exception instanceof QueryException) {
            $errorMsg = explode('(', $exception->getMessage());
            $details = ['FileName' => $exception->getFile(), 'LineNum' => $exception->getLine()];
            return ApiResponse::returnErrorMessage($errorMsg[0], $details);
        }*/

        # Sending Emails Error
        /*if($exception instanceof Swift_TransportException) {
            $errorMsg = explode('(', $exception->getMessage());
            return ApiResponse::returnErrorMessage($errorMsg[0]);
        }*/

        return parent::render($request, $exception);
    }
}
