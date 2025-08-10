<?php

namespace App\Controllers;

use App\Facades\CommunicationFacade;
use App\Services\EmailChannel;
use App\Services\SMSChannel;
use App\Services\MessagingService;
use App\Services\MessagingNotificationService;
use App\Services\RecipientService;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class CommunicationController
{
    private CommunicationFacade $communicationFacade;
    private PDO $pdo; // For passing to services that require it

    public function __construct()
    {
        $this->pdo = Db::connection(); // Get PDO connection

        // Instantiate dependencies for the Facade
        $emailChannel = new EmailChannel([
            'username' => 'chaimaelomrani6@gmail.com', // IMPORTANT: Replace with your actual email
            'password' => 'ryesdmadagrpmfuo', // IMPORTANT: Replace with your actual app password
            'from_email' => 'schoolmanagement@gmail.com',
        ]);
        $smsChannel = new SMSChannel();
        $messagingService = new MessagingService(); // MessagingService needs PDO
        $notificationRepository = new MessagingNotificationService();
        $recipientService = new RecipientService();

        // Instantiate the Facade with its dependencies
        $this->communicationFacade = new CommunicationFacade(
            $emailChannel,
            $smsChannel,
            $messagingService,
            $notificationRepository,
            $recipientService
        );
    }

    /**
     * Handles POST request to send an email notification.
     * Route: POST /api/communication/send-email
     * Request Body: { "to": "email@example.com", "subject": "Title", "message": "Content" }
     */
    public function sendEmailNotification(): void
    {
        $data = $this->getJsonInput();
        $to = $data['to'] ?? null;
        $subject = $data['subject'] ?? null;
        $message = $data['message'] ?? null;

        if (!$to || !$subject || !$message) {
            $this->sendJsonResponse(['error' => 'Missing required fields: to, subject, message.'], 400);
            return;
        }

        try {
            $success = $this->communicationFacade->sendNotificationByEmail($to, $subject, $message);
            if ($success) {
                $this->sendJsonResponse(['message' => 'Email notification sent successfully.']);
            } else {
                $this->sendJsonResponse(['error' => 'Failed to send email notification.'], 500);
            }
        } catch (\InvalidArgumentException $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handles POST request to send an SMS notification.
     * Route: POST /api/communication/send-sms
     * Request Body: { "phoneNumber": "+1234567890", "message": "Content" }
     */
    public function sendSMSNotification(): void
    {
        $data = $this->getJsonInput();
        $phoneNumber = $data['phoneNumber'] ?? null;
        $message = $data['message'] ?? null;

        if (!$phoneNumber || !$message) {
            $this->sendJsonResponse(['error' => 'Missing required fields: phoneNumber, message.'], 400);
            return;
        }

        try {
            $success = $this->communicationFacade->sendNotificationBySMS($phoneNumber, $message);
            if ($success) {
                $this->sendJsonResponse(['message' => 'SMS notification sent successfully.']);
            } else {
                $this->sendJsonResponse(['error' => 'Failed to send SMS notification.'], 500);
            }
        } catch (\InvalidArgumentException $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handles POST request to send an internal message.
     * Route: POST /api/communication/send-internal-message
     * Request Body: { "senderId": 1, "receiverId": 2, "subject": "Hello", "content": "Hi there!" }
     */
    public function sendInternalMessage(): void
    {
        $data = $this->getJsonInput();
        $senderId = $data['senderId'] ?? null;
        $receiverId = $data['receiverId'] ?? null;
        $subject = $data['subject'] ?? null;
        $content = $data['content'] ?? null;

        if (!$senderId || !$receiverId || !$subject || !$content) {
            $this->sendJsonResponse(['error' => 'Missing required fields: senderId, receiverId, subject, content.'], 400);
            return;
        }

        try {
            $success = $this->communicationFacade->sendInternalMessage((int)$senderId, (int)$receiverId, $subject, $content);
            if ($success) {
                $this->sendJsonResponse(['message' => 'Internal message sent successfully.']);
            } else {
                $this->sendJsonResponse(['error' => 'Failed to send internal message.'], 500);
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handles POST request to broadcast a notification to multiple recipients.
     * Route: POST /api/communication/broadcast-notification
     * Request Body: { "recipientIds": [1, 2], "title": "Update", "message": "System update.", "channels": ["email", "sms"] }
     */
    public function broadcastNotification(): void
    {
        $data = $this->getJsonInput();
        $recipientIds = $data['recipientIds'] ?? null;
        $title = $data['title'] ?? null;
        $message = $data['message'] ?? null;
        $channels = $data['channels'] ?? ['email']; // Default to email if not specified

        if (!is_array($recipientIds) || empty($recipientIds) || !$title || !$message) {
            $this->sendJsonResponse(['error' => 'Missing or invalid required fields: recipientIds (array), title, message.'], 400);
            return;
        }

        try {
            $results = $this->communicationFacade->broadcastNotification($recipientIds, $title, $message, $channels);
            $this->sendJsonResponse([
                'message' => 'Broadcast initiated.',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'An unexpected error occurred during broadcast: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to get JSON input from the request body.
     */
    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON input: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * Helper to send JSON responses.
     */
    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
