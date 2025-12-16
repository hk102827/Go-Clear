<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // API requests ke liye JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.'
            ], 401);
        }

        // Web requests ke liye admin login
        return redirect()->guest(route('admin.login'));
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // API requests ke liye
        if ($request->is('api/*') || $request->expectsJson()) {

            // Authentication Exception
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.'
                ], 401);
            }

            // Validation Exception
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $exception->errors()
                ], 422);
            }

            // Not Found Exception
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            // Generic Server Error
            return response()->json([
                'success' => false,
                'message' => config('app.debug') 
                    ? $exception->getMessage() 
                    : 'Server error'
            ], 500);
        }

        // Web requests ke liye default behavior
        return parent::render($request, $exception);
    }
}