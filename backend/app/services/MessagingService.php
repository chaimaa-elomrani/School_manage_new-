<?php

namespace App\Services;

use App\Interfaces\IMessaging;
use App\Interfaces\IMessage;
use App\Models\Message;
use PDO;

class MessagingService implements IMessaging
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function sendMessage(IMessage $message): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO messages (sender_id, receiver_id, subject, content, created_at) 
                VALUES (?, ?, ?, ?, ?)
            ');
            
            return $stmt->execute([
                $message->getSenderId(),
                $message->getReceiverId(),
                $message->getSubject(),
                $message->getContent(),
                $message->getCreatedAt()
            ]);
        } catch (\Exception $e) {
            error_log("Message sending failed: " . $e->getMessage());
            return false;
        }
    }

    public function getMessages(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM messages 
                WHERE receiver_id = ? OR sender_id = ? 
                ORDER BY created_at DESC
            ');
            $stmt->execute([$userId, $userId]);
            
            $messages = [];
            while ($row = $stmt->fetch()) {
                $messages[] = new Message($row);
            }
            
            return $messages;
        } catch (\Exception $e) {
            error_log("Failed to get messages: " . $e->getMessage());
            return [];
        }
    }

    public function getConversation(int $user1Id, int $user2Id): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM messages 
                WHERE (sender_id = ? AND receiver_id = ?) 
                   OR (sender_id = ? AND receiver_id = ?) 
                ORDER BY created_at ASC
            ');
            $stmt->execute([$user1Id, $user2Id, $user2Id, $user1Id]);
            
            $messages = [];
            while ($row = $stmt->fetch()) {
                $messages[] = new Message($row);
            }
            
            return $messages;
        } catch (\Exception $e) {
            error_log("Failed to get conversation: " . $e->getMessage());
            return [];
        }
    }

    public function markAsRead(int $messageId): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE messages 
                SET is_read = TRUE, read_at = NOW() 
                WHERE id = ?
            ');
            return $stmt->execute([$messageId]);
        } catch (\Exception $e) {
            error_log("Failed to mark message as read: " . $e->getMessage());
            return false;
        }
    }

    public function deleteMessage(int $messageId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM messages WHERE id = ?');
            return $stmt->execute([$messageId]);
        } catch (\Exception $e) {
            error_log("Failed to delete message: " . $e->getMessage());
            return false;
        }
    }
}