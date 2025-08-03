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
            $pdo = Db::connection();
            $this->paymentService = new PaymentService($pdo);
        }
    }

    public function getAll()
    {
        try {
            $payments = $this->paymentService->getAll();
            
            // Add CORS headers
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            
            echo json_encode([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}