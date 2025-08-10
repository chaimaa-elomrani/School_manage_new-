<?php

namespace App\Services;

use App\Models\Notification;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class MessagingNotificationService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function save(Notification $notification): Notification
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO notifications (title, message, type, status, created_at, sent_at) 
                    VALUES (:title, :message, :type, :status, :created_at, :sent_at)
                    ON CONFLICT (id) DO UPDATE SET
                        title = EXCLUDED.title,
                        message = EXCLUDED.message,
                        type = EXCLUDED.type,
                        status = EXCLUDED.status,
                        sent_at = EXCLUDED.sent_at,
                        updated_at = CURRENT_TIMESTAMP
                    RETURNING id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'status' => $notification->getStatus(),
                'created_at' => $notification->getCreatedAt(),
                'sent_at' => $notification->getSentAt(),
            ]);
            
            $notificationId = $stmt->fetchColumn();
            $this->pdo->commit();

            // Update the notification object with its new ID if it was an insert
            $data = $notification->toArray();
            $data['id'] = (int) $notificationId;
            return new Notification($data);

        } catch (\Exception $e) {
            $this->pdo->rollback();
            error_log("Failed to save notification: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById(int $id): ?Notification
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notifications WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Notification($row) : null;
    }

    public function listAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notifications ORDER BY created_at DESC');
        $stmt->execute();
        $notifications = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifications[] = new Notification($row);
        }
        return $notifications;
    }
}
