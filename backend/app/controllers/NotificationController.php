<?php

namespace App\Controllers;

use Core\Db;

class NotificationController
{
    public function getAll()
    {
        try {
            $pdo = Db::connection();
            $stmt = $pdo->prepare('
                SELECT n.*, nr.recipient_id 
                FROM notifications n 
                LEFT JOIN notification_recipients nr ON n.id = nr.notification_id 
                ORDER BY n.created_at DESC
            ');
            $stmt->execute();
            $notifications = $stmt->fetchAll();
            
            echo json_encode(['message' => 'Notifications retrieved', 'data' => $notifications]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}