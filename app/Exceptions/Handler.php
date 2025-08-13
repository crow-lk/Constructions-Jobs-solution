<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Check if this is an API request
            if (request()->is('api/*')) {
                Log::channel('api')->error('API Exception', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'method' => request()->method(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
        
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $statusCode = 500;
                
                // Get appropriate status code
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e->getCode() >= 100 && $e->getCode() < 600) {
                    $statusCode = $e->getCode();
                }
                
                // Format API exceptions in a consistent way
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Server Error',
                    'exception' => get_class($e),
                    'file' => config('app.debug') ? $e->getFile() : null,
                    'line' => config('app.debug') ? $e->getLine() : null,
                    'trace' => config('app.debug') ? explode("\n", $e->getTraceAsString()) : null,
                ], $statusCode);
            }
            
            return null;
        });
    }
}
