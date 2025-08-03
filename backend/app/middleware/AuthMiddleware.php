<?php

namespace App\Middleware;

use App\Services\AuthService;
use Core\Db;

class AuthMiddleware
{
    private $authService;

    public function __construct()
    {
        $pdo = Db::connection();
        $this->authService = new AuthService($pdo);
    }

    public function handle(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized - No token provided']);
            return false;
        }

        $token = substr($authHeader, 7);
        $payload = $this->authService->validateToken($token);
        
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized - Invalid token']);
            return false;
        }

        // Store user data in globals for access in controllers
        $GLOBALS['current_user'] = $payload;
        return true;
    }

    public static function requireRole(string $role): bool
    {
        $currentUser = $GLOBALS['current_user'] ?? null;
        
        if (!$currentUser || $currentUser['role'] !== $role) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden - Insufficient permissions']);
            return false;
        }
        
        return true;
    }
}