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
        try {
            // Set headers
            $this->setCorsHeaders();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new \Exception('Invalid JSON data');
            }

            // Validate required fields
            if (!isset($input['email']) || !isset($input['password'])) {
                throw new \Exception('Email and password are required');
            }

            $result = $this->authService->register($input);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function login()
    {
        try {
            // Set headers
            $this->setCorsHeaders();
            
            // Get request body
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                throw new \Exception('Invalid JSON data');
            }
            
            if (!isset($data['email']) || !isset($data['password'])) {
                throw new \Exception('Email and password are required');
            }

            // Attempt login
            $result = $this->authService->login($data['email'], $data['password']);
            
            if (!$result) {
                throw new \Exception('Invalid credentials');
            }

            // Log successful login for debugging
            error_log("Login successful for user: " . $data['email'] . " with role: " . $result['user']['role']);

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            error_log("Login failed: " . $e->getMessage());
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        try {
            $this->setCorsHeaders();
            
            // For JWT, logout is handled client-side by removing the token
            echo json_encode([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function me()
    {
        try {
            $this->setCorsHeaders();
            
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';
            
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                throw new \Exception('No token provided');
            }

            $token = substr($authHeader, 7);
            $payload = $this->authService->validateToken($token);
            
            if (!$payload) {
                throw new \Exception('Invalid token');
            }

            echo json_encode([
                'success' => true,
                'message' => 'User data retrieved',
                'data' => $payload
            ]);
            
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function setCorsHeaders(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}
