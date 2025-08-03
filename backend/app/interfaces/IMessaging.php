<?php

namespace App\Interfaces;

interface IMessaging
{
    public function sendMessage(IMessage $message): bool;
    public function getMessages(int $userId): array;
    public function getConversation(int $user1Id, int $user2Id): array;
    public function markAsRead(int $messageId): bool;
    public function deleteMessage(int $messageId): bool;
}