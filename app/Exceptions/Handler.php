<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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

        if ($exception instanceof APIException) {

            if ($exception->getCode() == 500) {
                logs()->error($exception->getMessage() . "\n" . $exception->getTraceAsString());
            }

            $data = [
                'success' => 'failed',
                'message' => $exception->getMessage(),
            ];
            if (env('APP_DEBUG')) {
                $data['trace'] = $exception->getTrace();
            }

            return response()->json(
                $data,
                $exception->getCode()
            );
        }


        return parent::render($request, $exception);

    }
}
