<?php

namespace App\Facades;

use App\Interfaces\ICommunicationFacade;
use App\Interfaces\IEmailChannel;
use App\Interfaces\ISMSChannel;
use App\Interfaces\IMessaging;
use App\Models\Notification;
use App\Models\Message;
use App\Services\NotificationService;
use PDO;

class CommunicationFacade implements ICommunicationFacade
{
    private $emailChannel;
    private $smsChannel;
    private $messagingService;
    private $notificationService;
    private $pdo;

    public function __construct(
        IEmailChannel $emailChannel,
        ISMSChannel $smsChannel,
        IMessaging $messagingService,
        NotificationService $notificationService,
        PDO $pdo
    ) {
        $this->emailChannel = $emailChannel;
        $this->smsChannel = $smsChannel;
        $this->messagingService = $messagingService;
        $this->notificationService = $notificationService;
        $this->pdo = $pdo;
    }

    public function sendNotificationByEmail(string $email, string $title, string $message): bool
    {
        try {
            // Validate email
            if (!$this->emailChannel->validateEmail($email)) {
                throw new \InvalidArgumentException("Invalid email address: $email");
            }

            // Create notification record
            $notification = new Notification([
                'title' => $title,
                'message' => $message,
                'type' => 'email'
            ]);

            // Send email
            $emailSent = $this->emailChannel->sendEmail($email, $title, $message);
            
            if ($emailSent) {
                $notification->send();
                error_log("Email notification sent successfully to: $email");
            } else {
                $notification->markAsFailed();
                error_log("Failed to send email notification to: $email");
            }

            // Save notification to database
            $this->saveNotification($notification);
            
            return $emailSent;
        } catch (\Exception $e) {
            error_log("Email notification error: " . $e->getMessage());
            return false;
        }
    }

    public function sendNotificationBySMS(string $phoneNumber, string $message): bool
    {
        try {
            // Validate phone number
            if (!$this->smsChannel->validatePhoneNumber($phoneNumber)) {
                throw new \InvalidArgumentException("Invalid phone number: $phoneNumber");
            }

            // Create notification record
            $notification = new Notification([
                'title' => 'SMS Notification',
                'message' => $message,
                'type' => 'sms'
            ]);

            // Send SMS
            $smsSent = $this->smsChannel->sendSMS($phoneNumber, $message);
            
            if ($smsSent) {
                $notification->send();
                error_log("SMS notification sent successfully to: $phoneNumber");
            } else {
                $notification->markAsFailed();
                error_log("Failed to send SMS notification to: $phoneNumber");
            }

            // Save notification to database
            $this->saveNotification($notification);
            
            return $smsSent;
        } catch (\Exception $e) {
            error_log("SMS notification error: " . $e->getMessage());
            return false;
        }
    }

    public function sendNotificationByBoth(string $email, string $phoneNumber, string $title, string $message): bool
    {
        $emailResult = $this->sendNotificationByEmail($email, $title, $message);
        $smsResult = $this->sendNotificationBySMS($phoneNumber, $message);
        
        return $emailResult && $smsResult;
    }

    public function sendInternalMessage(int $senderId, int $receiverId, string $subject, string $content): bool
    {
        try {
            $message = new Message([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'subject' => $subject,
                'content' => $content
            ]);

            $result = $this->messagingService->sendMessage($message);
            
            if ($result) {
                error_log("Internal message sent from user $senderId to user $receiverId");
            } else {
                error_log("Failed to send internal message from user $senderId to user $receiverId");
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Internal message error: " . $e->getMessage());
            return false;
        }
    }

    public function broadcastNotification(array $recipients, string $title, string $message, array $channels = ['email']): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $recipientResults = [];
            
            foreach ($channels as $channel) {
                switch ($channel) {
                    case 'email':
                        if (isset($recipient['email'])) {
                            $recipientResults['email'] = $this->sendNotificationByEmail(
                                $recipient['email'], 
                                $title, 
                                $message
                            );
                        }
                        break;
                        
                    case 'sms':
                        if (isset($recipient['phone'])) {
                            $recipientResults['sms'] = $this->sendNotificationBySMS(
                                $recipient['phone'], 
                                $message
                            );
                        }
                        break;
                        
                    case 'internal':
                        if (isset($recipient['user_id'])) {
                            $recipientResults['internal'] = $this->sendInternalMessage(
                                1, // System user ID
                                $recipient['user_id'], 
                                $title, 
                                $message
                            );
                        }
                        break;
                }
            }
            
            $results[$recipient['id'] ?? 'unknown'] = $recipientResults;
        }
        
        return $results;
    }

    private function saveNotification(Notification $notification): void
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO notifications (title, message, type, status, created_at, sent_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            
            $stmt->execute([
                $notification->getTitle(),
                $notification->getMessage(),
                $notification->getType(),
                $notification->getStatus(),
                $notification->getCreatedAt(),
                $notification->getStatus() === 'sent' ? date('Y-m-d H:i:s') : null
            ]);
        } catch (\Exception $e) {
            error_log("Failed to save notification: " . $e->getMessage());
        }
    }
}