<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Db;

class AuthController
{
    private $authService;

    public function __construct(AuthService $authService = null)
    {
        if ($authService) {
            $this->authService = $authService;
        } else {
            $pdo = Db::connection();
            $this->authService = new AuthService($pdo);
        }
    }

    public function register()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        // Validate required fields
        if (!isset($input['email']) || !isset($input['password'])) {
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        try {
            $result = $this->authService->register($input);
            echo json_encode([
                'message' => 'User registered successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        try {
            $result = $this->authService->login($input['email'], $input['password']);
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        // For JWT, logout is handled client-side by removing the token
        echo json_encode(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            echo json_encode(['error' => 'No token provided']);
            return;
        }

        $token = substr($authHeader, 7);
        $payload = $this->authService->validateToken($token);
        
        if (!$payload) {
            echo json_encode(['error' => 'Invalid token']);
            return;
        }

        echo json_encode([
            'message' => 'User data retrieved',
            'data' => $payload
        ]);
    }
}