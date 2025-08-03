<?php

namespace App\Interfaces;

interface INotification
{
    public function getId(): ?int;
    public function getTitle(): string;
    public function getMessage(): string;
    public function getType(): string;
    public function getCreatedAt(): string;
    public function send(): bool;
    public function toArray(): array;
}