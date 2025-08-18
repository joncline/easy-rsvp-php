<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use PDOException;
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Check if this is a database connection error
        if ($this->isDatabaseConnectionError($exception)) {
            return $this->renderDatabaseConnectionError($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Check if the exception is a database connection error
     */
    private function isDatabaseConnectionError(Throwable $exception): bool
    {
        if ($exception instanceof QueryException || $exception instanceof PDOException) {
            $message = $exception->getMessage();
            
            // Check for common connection error patterns
            return str_contains($message, 'Connection refused') ||
                   str_contains($message, 'Connection timed out') ||
                   str_contains($message, 'No such host') ||
                   str_contains($message, 'Access denied') ||
                   str_contains($message, 'Unknown database') ||
                   str_contains($message, 'SQLSTATE[HY000] [2002]') ||
                   str_contains($message, 'SQLSTATE[HY000] [1045]');
        }

        return false;
    }

    /**
     * Render a database connection error with debugging information
     */
    private function renderDatabaseConnectionError(Request $request, Throwable $exception)
    {
        // Get database configuration
        $dbConfig = [
            'connection' => config('database.default'),
            'host' => config('database.connections.' . config('database.default') . '.host'),
            'port' => config('database.connections.' . config('database.default') . '.port'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'username' => config('database.connections.' . config('database.default') . '.username'),
        ];

        // Get environment info
        $envInfo = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ];

        $errorData = [
            'exception' => $exception,
            'dbConfig' => $dbConfig,
            'envInfo' => $envInfo,
            'originalMessage' => $exception->getMessage(),
        ];

        // If debug mode is on or we're in local environment, show detailed error
        if (config('app.debug') || config('app.env') === 'local') {
            return response()->view('errors.database-connection', $errorData, 500);
        }

        // In production, log the details but show generic error
        logger()->error('Database connection error', [
            'message' => $exception->getMessage(),
            'db_config' => $dbConfig,
            'env' => $envInfo,
        ]);

        return response()->view('errors.500', ['exception' => $exception], 500);
    }
}
