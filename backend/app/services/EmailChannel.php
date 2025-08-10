<?php

namespace App\Services;

use App\Interfaces\IEmailChannel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailChannel implements IEmailChannel
{
    private array $config;

    public function __construct(array $config = [])
    {
        // Default configuration, can be overridden
        $this->config = array_merge([
            'host' => 'smtp.gmail.com',
            'smtp_auth' => true,
            'username' => 'your_email@gmail.com', // IMPORTANT: Replace with your actual email
            'password' => 'your_app_password', // IMPORTANT: Replace with your actual app password
            'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,
            'port' => 587,
            'from_email' => 'your_email@gmail.com',
            'from_name' => 'School Management System',
        ], $config);
    }

    public function sendEmail(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = $this->config['smtp_auth'];
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['smtp_secure'];
            $mail->Port = $this->config['port'];

            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
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
