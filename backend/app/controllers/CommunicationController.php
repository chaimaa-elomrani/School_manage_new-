<?php

namespace App\Controllers;

use App\Facades\CommunicationFacade;
use App\Services\EmailChannel;
use App\Services\SMSChannel;
use App\Services\MessagingService;
use App\Services\NotificationService;
use App\Services\StudentService;
use Core\Db;

class CommunicationController
{
    
    private $communicationFacade;

    public function __construct()
    {
        try {
            $pdo = Db::connection();
            
            // Load configurations with error handling
            $emailConfigPath = __DIR__ . '/../../config/email.php';
            $smsConfigPath = __DIR__ . '/../../config/sms.php';
            
            $emailConfig = file_exists($emailConfigPath) ? include $emailConfigPath : [];
            $smsConfig = file_exists($smsConfigPath) ? include $smsConfigPath : [];
            
            if (empty($emailConfig) || empty($smsConfig)) {
                error_log('Warning: Missing or empty configuration files');
            }
            
            // Initialize services
            $this->emailChannel = new EmailChannel($emailConfig);
            $this->smsChannel = new SMSChannel();
            $this->messagingService = new MessagingService($pdo);
            
        } catch (\Exception $e) {
            error_log('CommunicationController initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendEmailNotification()
    {
        try {
            // Set headers
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['email'], $input['title'], $input['message'])) {
                throw new \Exception('Missing required fields');
            }

            // Load email config
            $emailConfig = require __DIR__ . '/../../config/email.php';
            $emailChannel = new EmailChannel($emailConfig);

            // Validate email
            if (!$emailChannel->validateEmail($input['email'])) {
                throw new \Exception('Invalid email address');
            }

            // Send email
            $result = $emailChannel->sendEmail(
                $input['email'],
                $input['title'],
                $input['message']
            );

            // Only return success if email was actually sent
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email sent successfully'
                ]);
            } else {
                throw new \Exception('Failed to send email');
            }

        } catch (\Exception $e) {
            error_log("Email error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendSMSNotification()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['phone'], $input['message'])) {
                throw new \Exception('Missing required fields');
            }

            $smsChannel = new SMSChannel();
            $result = $smsChannel->sendSMS(
                $input['phone'],
                $input['message']
            );

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'SMS sent successfully'
                ]);
            } else {
                throw new \Exception('Failed to send SMS');
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendInternalMessage()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['sender_id'], $input['receiver_id'], $input['subject'], $input['content'])) {
                throw new \Exception('Missing required fields');
            }

            $pdo = Db::connection();
            $messagingService = new MessagingService($pdo);
            
            $message = new \App\Models\Message([
                'sender_id' => $input['sender_id'],
                'receiver_id' => $input['receiver_id'],
                'subject' => $input['subject'],
                'content' => $input['content'],
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $result = $messagingService->sendMessage($message);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Internal message sent successfully'
                ]);
            } else {
                throw new \Exception('Failed to send internal message');
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function broadcastNotification()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['recipients'], $input['title'], $input['message'])) {
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $channels = $input['channels'] ?? ['email'];
        
        $results = $this->communicationFacade->broadcastNotification(
            $input['recipients'],
            $input['title'],
            $input['message'],
            $channels
        );

        echo json_encode([
            'success' => true,
            'message' => 'Broadcast completed',
            'results' => $results
        ]);
    }
}






