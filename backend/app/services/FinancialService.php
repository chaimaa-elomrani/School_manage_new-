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
    
    public function getAllPayments(): array
    {
        try {
            $payments = $this->paymentRepository->getAll();
            
            // Transform the payments to include all necessary data
            return array_map(function($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'amount' => number_format($payment->amount, 2, '.', ''),
                    'status' => $payment->status,
                    'payment_date' => $payment->payment_date,
                    'due_date' => $payment->due_date,
                    'fee_name' => $payment->fee_name,
                    'student_name' => $payment->student ? 
                        $payment->student->first_name . ' ' . $payment->student->last_name : 
                        'Unknown Student'
                ];
            }, $payments);
        } catch (\Exception $e) {
            error_log('Error fetching payments: ' . $e->getMessage());
            return [];
        }
    }
}