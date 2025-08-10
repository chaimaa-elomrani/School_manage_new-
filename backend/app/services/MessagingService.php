<?php

namespace App\Services;

use App\Models\Message;
use PDO;
use Core\Db; // Assuming Core\Db for connection

/**
 * Handles the storage and retrieval of internal messages.
 * SRP: Manages internal messaging, distinct from external notifications.
 */
class MessagingService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function sendMessage(Message $message): Message
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO messages (sender_id, receiver_id, subject, content, created_at, is_read) 
                VALUES (:sender_id, :receiver_id, :subject, :content, :created_at, :is_read)
            ');
            $stmt->execute([
                'sender_id' => $message->getSenderId(),
                'receiver_id' => $message->getReceiverId(),
                'subject' => $message->getSubject(),
                'content' => $message->getContent(),
                'created_at' => $message->getCreatedAt(),
                'is_read' => $message->isRead() ? 1 : 0, // Convert bool to int for DB
            ]);
            $messageId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $message->toArray();
            $data['id'] = (int) $messageId;
            return new Message($data);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getMessages(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM messages 
            WHERE receiver_id = :user_id OR sender_id = :user_id 
            ORDER BY created_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        $messages = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $messages[] = new Message($row);
        }
        return $messages;
    }

    public function getConversation(int $user1Id, int $user2Id): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM messages 
            WHERE (sender_id = :user1_id AND receiver_id = :user2_id) 
               OR (sender_id = :user2_id AND receiver_id = :user1_id) 
            ORDER BY created_at ASC
        ');
        $stmt->execute(['user1_id' => $user1Id, 'user2_id' => $user2Id]);
        $messages = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $messages[] = new Message($row);
        }
        return $messages;
    }

    public function markAsRead(int $messageId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE messages SET is_read = TRUE, read_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $messageId]);
        return $stmt->rowCount() > 0;
    }

    public function deleteMessage(int $messageId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM messages WHERE id = :id');
        $stmt->execute(['id' => $messageId]);
        return $stmt->rowCount() > 0;
    }
}
