<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if(
            $exception instanceof \App\Exceptions\Client\EventExcecutorAppendException ||
            $exception instanceof \App\Exceptions\Client\EventExcecutorDetachException ||
            $exception instanceof \App\Exceptions\Client\EventReporterAttachException ||
            $exception instanceof \App\Exceptions\Client\EventReporterIsAuthorException ||
            $exception instanceof \App\Exceptions\Client\EventReporterNotException ||
            $exception instanceof \App\Exceptions\Client\EventCloseIsWorking ||
            $exception instanceof \App\Exceptions\Client\EventCloseNotWhileIsNew
        )
            return $exception->render();


        if($exception instanceof ValidationException) {
            $errors = [];

            foreach($exception->errors() as $item)
                $errors[] = $item[0];

            return response()->json([
                'message' => implode(PHP_EOL,$errors),
                'success' => 0,
                'error' => implode(', ', [
                    'Фаил где поймал исключение: '.$exception->getFile(),
                    'Cтрока с исключением: '.$exception->getLine(),
                ])
            ], 415);
        }



        return response()->json([
            'message' => $exception->getMessage(),
            'success' => 0,
            'error' => implode(', ', [
                'Фаил где поймал исключение: '.$exception->getFile(),
                'Cтрока с исключением: '.$exception->getLine(),
            ])
        ], 404);
    }
}
