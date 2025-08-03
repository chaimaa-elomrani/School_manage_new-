<?php

namespace App\Interfaces;

interface IMessage
{
    public function getId(): ?int;
    public function getSenderId(): int;
    public function getReceiverId(): int;
    public function getSubject(): string;
    public function getContent(): string;
    public function isRead(): bool;
    public function markAsRead(): void;
    public function getCreatedAt(): string;
    public function toArray(): array;
}