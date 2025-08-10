<?php

namespace App\Facades;

use App\Models\Notification;
use App\Models\Message;
use App\Models\Recipient;
use App\Services\MessagingNotificationService;
use App\Services\MessagingService;
use App\Services\EmailChannel;
use App\Services\SMSChannel;
use App\Services\RecipientService;
use App\Interfaces\IEmailChannel;
use App\Interfaces\ISMSChannel;
use PDO; // Still needed for MessagingService if it's not refactored to use a repository

class CommunicationFacade
{
    private IEmailChannel $emailChannel;
    private ISMSChannel $smsChannel;
    private MessagingService $messagingService;
    private MessagingNotificationService $notificationRepository;
    private RecipientService $recipientService;

    public function __construct(
        IEmailChannel $emailChannel,
        ISMSChannel $smsChannel,
        MessagingService $messagingService,
        MessagingNotificationService $notificationRepository,
        RecipientService $recipientService
    ) {
        $this->emailChannel = $emailChannel;
        $this->smsChannel = $smsChannel;
        $this->messagingService = $messagingService;
        $this->notificationRepository = $notificationRepository;
        $this->recipientService = $recipientService;
    }

    /**
     * Sends an email notification and records it.
     */
    public function sendNotificationByEmail(string $toEmail, string $title, string $messageContent): bool
    {
        try {
            if (!$this->emailChannel->validateEmail($toEmail)) {
                throw new \InvalidArgumentException("Invalid email address: $toEmail");
            }

            $notification = new Notification([
                'title' => $title,
                'message' => $messageContent,
                'type' => 'email',
                'status' => 'pending'
            ]);

            $emailSent = $this->emailChannel->sendEmail($toEmail, $title, $messageContent);

            if ($emailSent) {
                $notification->markAsSent();
                error_log("Email notification sent successfully to: $toEmail");
            } else {
                $notification->markAsFailed();
                error_log("Failed to send email notification to: $toEmail");
            }

            $this->notificationRepository->save($notification);
            return $emailSent;
        } catch (\Exception $e) {
            error_log("Email notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sends an SMS notification and records it.
     */
    public function sendNotificationBySMS(string $phoneNumber, string $messageContent): bool
    {
        try {
            if (!$this->smsChannel->validatePhoneNumber($phoneNumber)) {
                throw new \InvalidArgumentException("Invalid phone number: $phoneNumber");
            }

            $notification = new Notification([
                'title' => 'SMS Notification',
                'message' => $messageContent,
                'type' => 'sms',
                'status' => 'pending'
            ]);

            $smsSent = $this->smsChannel->sendSMS($phoneNumber, $messageContent);

            if ($smsSent) {
                $notification->markAsSent();
                error_log("SMS notification sent successfully to: $phoneNumber");
            } else {
                $notification->markAsFailed();
                error_log("Failed to send SMS notification to: $phoneNumber");
            }

            $this->notificationRepository->save($notification);
            return $smsSent;
        } catch (\Exception $e) {
            error_log("SMS notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sends an internal message and records it.
     */
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

        if ($result instanceof Message) {
            error_log("Internal message sent from user $senderId to user $receiverId");
            return true;
        } else {
            error_log("Failed to send internal message from user $senderId to user $receiverId");
            return false;
        }
    } catch (\Exception $e) {
        error_log("Internal message error: " . $e->getMessage());
        return false;
    }
}

    /**
     * Broadcasts a notification to multiple recipients via specified channels.
     * @param int[] $recipientIds Array of recipient IDs.
     * @param string $title Notification title.
     * @param string $message Notification message.
     * @param string[] $channels Array of channels to use (e.g., ['email', 'sms', 'internal']).
     * @return array Results for each recipient and channel.
     */
    public function broadcastNotification(array $recipientIds, string $title, string $message, array $channels = ['email']): array
    {
        $results = [];
        $recipients = $this->recipientService->getRecipientsByIds($recipientIds);

        foreach ($recipients as $recipient) {
            $recipientResults = [];
            foreach ($channels as $channel) {
                switch ($channel) {
                    case 'email':
                        if ($recipient->getEmail()) {
                            $recipientResults['email'] = $this->sendNotificationByEmail(
                                $recipient->getEmail(),
                                $title,
                                $message
                            );
                        } else {
                            $recipientResults['email'] = false;
                            error_log("Recipient ID {$recipient->getId()} has no email for email notification.");
                        }
                        break;
                    case 'sms':
                        if ($recipient->getPhoneNumber()) {
                            $recipientResults['sms'] = $this->sendNotificationBySMS(
                                $recipient->getPhoneNumber(),
                                $message
                            );
                        } else {
                            $recipientResults['sms'] = false;
                            error_log("Recipient ID {$recipient->getId()} has no phone number for SMS notification.");
                        }
                        break;
                    case 'internal':
                        // Assuming system user ID is 1 for internal broadcasts
                        $recipientResults['internal'] = $this->sendInternalMessage(
                            1, // System user ID
                            $recipient->getId(),
                            $title,
                            $message
                        );
                        break;
                    default:
                        error_log("Unknown communication channel: $channel");
                        $recipientResults[$channel] = false;
                        break;
                }
            }
            $results[$recipient->getId()] = $recipientResults;
        }
        return $results;
    }
}
