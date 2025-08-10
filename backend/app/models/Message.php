<?php

namespace App\Models;

class Message
{
    private ?int $id;
    private int $senderId;
    private int $receiverId;
    private string $subject;
    private string $content;
    private string $createdAt;
    private bool $isRead;
    private ?string $readAt;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->senderId = $data['sender_id'];
        $this->receiverId = $data['receiver_id'];
        $this->subject = $data['subject'];
        $this->content = $data['content'];
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->isRead = $data['is_read'] ?? false;
        $this->readAt = $data['read_at'] ?? null;
    }

    public function getId(): ?int { return $this->id; }
    public function getSenderId(): int { return $this->senderId; }
    public function getReceiverId(): int { return $this->receiverId; }
    public function getSubject(): string { return $this->subject; }
    public function getContent(): string { return $this->content; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function isRead(): bool { return $this->isRead; }
    public function getReadAt(): ?string { return $this->readAt; }

    public function markAsRead(): void
    {
        $this->isRead = true;
        $this->readAt = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'subject' => $this->subject,
            'content' => $this->content,
            'created_at' => $this->createdAt,
            'is_read' => $this->isRead,
            'read_at' => $this->readAt,
        ];
    }
}
