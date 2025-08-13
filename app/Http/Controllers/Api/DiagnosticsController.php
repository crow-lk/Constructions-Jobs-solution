<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiagnosticsController extends Controller
{
    /**
     * Run comprehensive API diagnostics
     */
    public function apiDiagnostics(Request $request): JsonResponse
    {
        Log::channel('daily')->info('API diagnostics started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $results = [
            'server_info' => $this->getServerInfo(),
            'request_info' => $this->getRequestInfo($request),
            'database_check' => $this->checkDatabase(),
            'storage_check' => $this->checkStorage(),
            'environment' => $this->getEnvironment(),
        ];
        
        Log::channel('daily')->info('API diagnostics completed', $results);
        
        return response()->json([
            'status' => 'success',
            'message' => 'API diagnostics completed successfully',
            'diagnostics' => $results,
        ]);
    }
    
    /**
     * Get server information
     */
    private function getServerInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
            'request_time' => $_SERVER['REQUEST_TIME'] ?? time(),
            'max_upload_size' => ini_get('upload_max_filesize'),
            'max_post_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
        ];
    }
    
    /**
     * Get request information
     */
    private function getRequestInfo(Request $request): array
    {
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'authorization' => $request->hasHeader('Authorization') ? 'Present' : 'Missing',
        ];
    }
    
    /**
     * Check database connection
     */
    private function checkDatabase(): array
    {
        try {
            $result = DB::select('SELECT 1');
            $connection = true;
            $error = null;
            
            // Try a quick test query on the users table
            try {
                $userCount = DB::table('users')->count();
                $tableAccess = true;
                $tableError = null;
            } catch (\Exception $e) {
                $tableAccess = false;
                $tableError = $e->getMessage();
            }
            
        } catch (\Exception $e) {
            $connection = false;
            $error = $e->getMessage();
            $tableAccess = false;
            $tableError = 'Database connection failed';
            $userCount = null;
        }
        
        return [
            'connection_successful' => $connection,
            'connection_error' => $error,
            'config' => [
                'driver' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
            ],
            'table_access' => $tableAccess,
            'table_error' => $tableError,
            'user_count' => $userCount ?? null,
        ];
    }
    
    /**
     * Check storage access
     */
    private function checkStorage(): array
    {
        $publicAccess = false;
        $publicError = null;
        
        try {
            // Create a test file
            $testContent = 'API Diagnostics Test - ' . date('Y-m-d H:i:s');
            $testFile = 'diagnostics/test_' . time() . '.txt';
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->put($testFile, $testContent)) {
                $publicAccess = true;
                
                // Clean up
                \Illuminate\Support\Facades\Storage::disk('public')->delete($testFile);
            } else {
                $publicError = 'Failed to write test file';
            }
        } catch (\Exception $e) {
            $publicError = $e->getMessage();
        }
        
        return [
            'public_disk_writable' => $publicAccess,
            'public_disk_error' => $publicError,
            'storage_path' => storage_path(),
            'public_path' => public_path(),
            'public_disk_path' => storage_path('app/public'),
            'symbolic_link_exists' => file_exists(public_path('storage')),
        ];
    }
    
    /**
     * Get environment information
     */
    private function getEnvironment(): array
    {
        return [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'cors' => [
                'paths' => config('cors.paths'),
                'allowed_origins' => config('cors.allowed_origins'),
                'allowed_methods' => config('cors.allowed_methods'),
                'allowed_headers' => config('cors.allowed_headers'),
            ],
            'logging_channel' => config('logging.default'),
        ];
    }
}
