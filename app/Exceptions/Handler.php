<?php

namespace App\Exceptions;

use App\Exceptions\Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    public function report(Throwable $exception)
    {
        // Report to Sentry
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }

    /**
     * Render exception.
     * Add error_code to json response.
     *
     * @param $request
     * @param Throwable $exception
     * @return mix
     */
    public function render($request, Throwable $exception)
    {
        $response = parent::render($request, $exception);
        if ($response instanceof JsonResponse) {
            $errorCode = null;
            if ($exception instanceof Exception) {
                $errorCode = $exception->getErrorCode();
            }
            if (!$errorCode) {
                $errorCode = $exception->getCode();
            }
            if ($errorCode) {
                $currentData = $response->getData(true);
                $currentData['error_code'] = $errorCode;
                $response->setData($currentData);
            }
        }
        return $response;
    }
}
