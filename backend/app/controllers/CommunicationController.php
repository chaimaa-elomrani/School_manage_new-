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
        $pdo = Db::connection();
        
        // Load configurations
        $emailConfig = include __DIR__ . '/../../config/email.php';
        $smsConfig = include __DIR__ . '/../../config/sms.php';
        
        // Initialize channels and services
        $emailChannel = new EmailChannel($emailConfig);
        $smsChannel = new SMSChannel();
        $messagingService = new MessagingService($pdo);
        $notificationService = new NotificationService($pdo);
        
        // Initialize facade
        $this->communicationFacade = new CommunicationFacade(
            $emailChannel,
            $smsChannel,
            $messagingService,
            $notificationService,
            $pdo
        );
    }

    public function sendEmailNotification()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['email'], $input['title'], $input['message'])) {
                throw new \Exception('Missing required fields');
            }

            // Configure PHPMailer
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Update with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Update with your email
            $mail->Password = 'your-password'; // Update with your password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'School System');
            $mail->addAddress($input['email']);
            $mail->Subject = $input['title'];
            $mail->Body = $input['message'];

            if ($mail->send()) {
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

            $smsChannel = new \App\Services\SMSChannel();
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






