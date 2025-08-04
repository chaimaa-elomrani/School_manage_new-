<?php

namespace App\Controllers;
use App\Services\PaymentService;
use Core\Db;

class PaymentController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService = null)
    {
        if ($paymentService) {
            $this->paymentService = $paymentService;
        } else {
            try {
                $pdo = Db::connection();
                $this->paymentService = new PaymentService($pdo);
            } catch (\Exception $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    public function getAll()
    {
        try {
            // Set headers first to avoid "headers already sent" errors
            if (!headers_sent()) {
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                header('Access-Control-Allow-Headers: Content-Type, Authorization');
            }

            // Get payments with error checking
            $payments = $this->paymentService->getAll();
            
            if (!is_array($payments)) {
                throw new \Exception('Invalid payment data format');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\PDOException $e) {
            error_log('Database error in PaymentController: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database error occurred',
                'details' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            error_log('Payment controller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ]);
        }
    }

    // Add an OPTIONS method handler for CORS
    public function options()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('HTTP/1.1 200 OK');
        exit();
    }
}