<?php

namespace App\Services;

use App\Interfaces\IPaymentNotificationService;
use PDO;

class PaymentNotificationService implements IPaymentNotificationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function notifyPaymentCreated($paymentData): void
    {
        $message = "Payment created: Amount " . $paymentData['total_amount'];
        $this->createNotification('Payment Created', $message);
        error_log("Payment notification: $message");
    }

    public function notifyPaymentCompleted($paymentData): void
    {
        $message = "Payment completed: Amount " . $paymentData['total_amount'];
        $this->createNotification('Payment Completed', $message);
        error_log("Payment completion notification: $message");
    }

    private function createNotification($title, $message): void
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO notifications (title, message) VALUES (?, ?)');
            $stmt->execute([$title, $message]);
        } catch (\Exception $e) {
            error_log("Notification creation failed: " . $e->getMessage());
        }
    }
}