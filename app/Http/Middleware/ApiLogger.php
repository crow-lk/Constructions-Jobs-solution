<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start timing the request
        $startTime = microtime(true);
        
        // Log the incoming request details
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'uri' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->filterHeaders($request->headers->all()),
            'body' => $this->filterRequestData($request->all()),
        ]);
        
        // Process the request
        $response = $next($request);
        
        // Calculate the request duration
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log the response
        Log::channel('api')->info('API Response', [
            'method' => $request->method(),
            'uri' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'response' => $this->extractResponseData($response),
        ]);
        
        return $response;
    }
    
    /**
     * Filter sensitive data from request headers
     */
    protected function filterHeaders(array $headers): array
    {
        $filtered = [];
        $sensitiveHeaders = ['authorization', 'cookie', 'x-xsrf-token'];
        
        foreach ($headers as $key => $value) {
            $lowercaseKey = strtolower($key);
            if (in_array($lowercaseKey, $sensitiveHeaders)) {
                $filtered[$key] = 'REDACTED';
            } else {
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Filter sensitive data from request body
     */
    protected function filterRequestData(array $data): array
    {
        $filtered = [];
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_token', 'credit_card'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $filtered[$key] = 'REDACTED';
            } else {
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Extract response data for logging
     */
    protected function extractResponseData(Response $response): array
    {
        // Don't try to decode non-JSON responses
        if (!$this->isJsonResponse($response)) {
            return [
                'content_type' => $response->headers->get('Content-Type'),
                'size' => $response->headers->get('Content-Length') ?? 'unknown',
            ];
        }
        
        $content = $response->getContent();
        
        try {
            $data = json_decode($content, true);
            
            // If it's an error response, include the full details for debugging
            if ($response->getStatusCode() >= 400) {
                return $data;
            }
            
            // For successful responses, just include some basic metadata to avoid bloating the logs
            return [
                'status' => $data['status'] ?? null,
                'message' => $data['message'] ?? null,
                'has_data' => isset($data['data']) ? true : false,
                'has_errors' => isset($data['errors']) ? true : false,
            ];
        } catch (\Exception $e) {
            return ['error' => 'Could not parse response JSON: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if the response is JSON
     */
    protected function isJsonResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        return $contentType && (
            strpos($contentType, 'application/json') !== false ||
            strpos($contentType, 'text/json') !== false
        );
    }
}
