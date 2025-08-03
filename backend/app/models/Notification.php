<?php

namespace App\Models;

use App\Interfaces\INotification;

class Notification implements INotification
{
    private $id;
    private $title;
    private $message;
    private $type;
    private $recipient_id;
    private $created_at;
    private $sent_at;
    private $status;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->type = $data['type'] ?? 'info';
        $this->recipient_id = $data['recipient_id'] ?? null;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->sent_at = $data['sent_at'] ?? null;
        $this->status = $data['status'] ?? 'pending';
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getMessage(): string { return $this->message; }
    public function getType(): string { return $this->type; }
    public function getRecipientId(): ?int { return $this->recipient_id; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getStatus(): string { return $this->status; }

    public function send(): bool
    {
        $this->status = 'sent';
        $this->sent_at = date('Y-m-d H:i:s');
        return true;
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'recipient_id' => $this->recipient_id,
            'created_at' => $this->created_at,
            'sent_at' => $this->sent_at,
            'status' => $this->status
        ];
    }
}