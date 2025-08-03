<?php

namespace App\Models;

use App\Interfaces\IMessage;

class Message implements IMessage
{
    private $id;
    private $sender_id;
    private $receiver_id;
    private $subject;
    private $content;
    private $is_read;
    private $created_at;
    private $read_at;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->sender_id = $data['sender_id'];
        $this->receiver_id = $data['receiver_id'];
        $this->subject = $data['subject'] ?? '';
        $this->content = $data['content'];
        $this->is_read = $data['is_read'] ?? false;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->read_at = $data['read_at'] ?? null;
    }

    public function getId(): ?int { return $this->id; }
    public function getSenderId(): int { return $this->sender_id; }
    public function getReceiverId(): int { return $this->receiver_id; }
    public function getSubject(): string { return $this->subject; }
    public function getContent(): string { return $this->content; }
    public function isRead(): bool { return $this->is_read; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getReadAt(): ?string { return $this->read_at; }

    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->read_at = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'subject' => $this->subject,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'read_at' => $this->read_at
        ];
    }
}