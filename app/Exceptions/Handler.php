<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($this->is404($exception)) {
            $this->log404($request);
        }
        return parent::render($request, $exception);
    }

    /////////////////////////////////////////////////////////////////////////////////////////

    private function isAuth($exception)
    {
        return $exception instanceof \Illuminate\Auth\AuthenticationException
            || $exception instanceof \Illuminate\Auth\Access\AuthorizationException;
    }

    private function is404($exception)
    {
        return $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
            || $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    }

    private function log404($request)
    {
        $error = [
            'url'    => $request->url(),
            'method' => $request->method(),
        ];

        $message = '404: ' . $error['url'] . "\n" . json_encode($error, JSON_PRETTY_PRINT);

        Log::debug($message);
    }

    /////////////////////////////////////////////////////////////////////////////////////////

    public function getClientIp() {

        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /////////////////////////////////////////////////////////////////////////////////////////

    public function unauthenticated($request=false, $exception=false)
    {
        // return ''; // use redirect('/login') or something if you want to redirect to login.
        return redirect('/login');
    }


}
