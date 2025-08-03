<?php

namespace App\Controllers;

use App\Models\PaiementEleve;
use App\Interfaces\IFinancialService;
use App\Interfaces\IPaymentRepository;
use App\Services\FinancialService;
use App\Services\PaymentNotificationService;
use App\Repositories\PaymentRepository;
use Core\Db;
use App\Factories\PaymentDecoratorFactory;

class PaiementEleveController
{
    private $financialService;
    private $paymentRepository;

    public function __construct(
        IFinancialService $financialService = null,
        IPaymentRepository $paymentRepository = null
    ) {
        if ($financialService && $paymentRepository) {
            $this->financialService = $financialService;
            $this->paymentRepository = $paymentRepository;
        } else {
            // Default dependency injection
            $pdo = Db::connection();
            $this->paymentRepository = new PaymentRepository($pdo, 'payments');
            $notificationService = new PaymentNotificationService($pdo);
            $this->financialService = new FinancialService($this->paymentRepository, $notificationService);
        }
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $payment = new PaiementEleve($input);
            
            // Apply decorators if configuration is provided
            if (isset($input['payment_config'])) {
                $decoratedPayment = PaymentDecoratorFactory::createDecoratedPayment($payment, $input['payment_config']);
                
                // Update original payment with decorated values
                if (method_exists($decoratedPayment, 'getDiscountAmount')) {
                    $payment->applyDiscount($decoratedPayment->getDiscountAmount());
                }
                if (method_exists($decoratedPayment, 'getLateFee')) {
                    $payment->applyExtraFee($decoratedPayment->getLateFee());
                }
            } else {
                // Legacy support for direct discount/extra_fee
                if (isset($input['discount'])) {
                    $payment->applyDiscount($input['discount']);
                }
                if (isset($input['extra_fee'])) {
                    $payment->applyExtraFee($input['extra_fee']);
                }
            }

            $paymentId = $this->financialService->processPayment($payment);
            
            echo json_encode([
                'message' => 'Payment created successfully',
                'payment_id' => $paymentId,
                'data' => $payment->toArray(),
                'description' => $payment->getDescription()
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $payments = $this->paymentRepository->getAll();
            $paymentsArray = [];
            foreach ($payments as $paymentData) {
                $payment = new PaiementEleve($paymentData);
                $paymentsArray[] = $payment->toArray();
            }
            echo json_encode(['message' => 'Payments retrieved successfully', 'data' => $paymentsArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id)
    {
        try {
            $paymentData = $this->paymentRepository->getById($id);
            if ($paymentData) {
                $payment = new PaiementEleve($paymentData);
                echo json_encode(['message' => 'Payment found', 'data' => $payment->toArray()]);
            } else {
                echo json_encode(['error' => 'Payment not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
