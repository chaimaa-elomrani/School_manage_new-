<?php

namespace App\Services;

use App\Interfaces\IFinancialService;
use App\Interfaces\IPaymentRepository;
use App\Interfaces\IPaymentNotificationService;

class FinancialService implements IFinancialService
{
    private $paymentRepository;
    private $notificationService;

    public function __construct(
        IPaymentRepository $paymentRepository,
        IPaymentNotificationService $notificationService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->notificationService = $notificationService;
    }

    public function processPayment($payment)
    {
        // Process the payment
        $paymentId = $this->paymentRepository->save($payment);
        
        // Notify about payment creation
        $this->notificationService->notifyPaymentCreated($payment->toArray());
        
        return $paymentId;
    }

    public function calculateTotalWithTax($amount, $taxRate = 0.0): float
    {
        return $amount * (1 + $taxRate);
    }

    public function generatePaymentReport($paymentId): array
    {
        $payment = $this->paymentRepository->getById($paymentId);
        
        return [
            'payment_id' => $paymentId,
            'payment_data' => $payment,
            'generated_at' => date('Y-m-d H:i:s'),
            'report_type' => 'payment_summary'
        ];
    }
}