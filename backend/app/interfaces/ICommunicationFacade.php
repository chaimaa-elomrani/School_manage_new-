<?php

namespace App\Interfaces;

interface ICommunicationFacade
{
    public function sendNotificationByEmail(string $email, string $title, string $message): bool;
    public function sendNotificationBySMS(string $phoneNumber, string $message): bool;
    public function sendNotificationByBoth(string $email, string $phoneNumber, string $title, string $message): bool;
    public function sendInternalMessage(int $senderId, int $receiverId, string $subject, string $content): bool;
    public function broadcastNotification(array $recipients, string $title, string $message, array $channels = ['email']): array;
}