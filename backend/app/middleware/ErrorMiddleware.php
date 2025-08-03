<?php

namespace App\Middleware;

class ErrorMiddleware
{
    public function handle($request, $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}