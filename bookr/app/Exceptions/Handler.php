<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->wantsJson() && !($e instanceof ValidationException)) {
            $response = [
                'message' => (string) $e->getMessage(),
                'status' => 400
];
            if ($e instanceof HttpException) {
                $response['message'] = Response::$statusTexts[$e->getStatusCode()];
                $response['status'] = $e->getStatusCode();
            } elseif ($e instanceof ModelNotFoundException) {
                $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
                $response['status'] = Response::HTTP_NOT_FOUND;
            }
            if ($this->isDebugMode()) {
                $response['debug'] = [
                    'exception' => get_class($e),
                    'trace' => $e->getTrace()
                ];
            }
            return response()->json(['error' => $response], $response['status']);
        }
        return parent::render($request, $e);
    }
    
    /**
    * Determine if the application is in debug mode.
    *
    * @return Boolean
    */
    public function isDebugMode()
    {
        return (Boolean) env('APP_DEBUG');
    }
}
