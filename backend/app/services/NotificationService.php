<?php

namespace App\Services;

use PDO;

class NotificationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function notifyParentsAndStudent($studentId, $message): void
    {
        try {
            // For now, just log the notification (you can extend this later)
            error_log("Notification for student $studentId: $message");
            
            // Simple notification storage
            $stmt = $this->pdo->prepare('INSERT INTO notifications (title, message) VALUES (?, ?)');
            $stmt->execute(['Grade Notification', $message]);
            
        } catch (\Exception $e) {
            error_log("Notification error: " . $e->getMessage());
        }
    }
}