<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Interfaces\IEmailChannel;

class EmailChannel implements IEmailChannel
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function sendEmail(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
         
            // Recipients
            $mail->setFrom('chaimaelomrani6@gmail.com', 'School Management');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $result = $mail->send();
            error_log("Email sent to: $to - Result: " . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $e->getMessage());
            return false;
        }
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}




