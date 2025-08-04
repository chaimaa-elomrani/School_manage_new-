<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Db;

class AuthController
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        if ($authService === null) {
            $pdo = Db::connection();
            $this->authService = new AuthService($pdo);
        } else {
            $this->authService = $authService;
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
        try {
            // Set headers
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            // Get request body
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                throw new \Exception('Email and password are required');
            }

            // Attempt login
            $result = $this->authService->login($data['email'], $data['password']);

            if (!$result) {
                throw new \Exception('Invalid credentials');
            }

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
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