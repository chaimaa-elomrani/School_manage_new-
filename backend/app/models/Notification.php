<?php

namespace App\Models;

class Notification
{
    private ?int $id;
    private string $title;
    private string $message;
    private string $type; // e.g., 'email', 'sms', 'internal'
    private string $status; // e.g., 'pending', 'sent', 'failed'
    private string $createdAt;
    private ?string $sentAt;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'];
        $this->message = $data['message'];
        $this->type = $data['type'];
        $this->status = $data['status'] ?? 'pending';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->sentAt = $data['sent_at'] ?? null;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getMessage(): string { return $this->message; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getSentAt(): ?string { return $this->sentAt; }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->sentAt = date('Y-m-d H:i:s');
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
        $this->sentAt = null; // Reset sentAt if it failed
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'sent_at' => $this->sentAt,
        ];
    }
}
