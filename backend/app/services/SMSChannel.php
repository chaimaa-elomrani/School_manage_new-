<?php

namespace App\Services;

use App\Interfaces\ISMSChannel;

class SMSChannel implements ISMSChannel
{
    public function sendSMS(string $phoneNumber, string $message): bool
    {
        try {
            error_log("Attempting to send SMS to: $phoneNumber");
            error_log("Message: $message");
            
            // For now, let's simulate SMS sending and log it
            // You can replace this with real SMS API later
            
            // Validate phone number
            if (!$this->validatePhoneNumber($phoneNumber)) {
                error_log("Invalid phone number: $phoneNumber");
                return false;
            }
            
            // Simulate SMS sending (replace with real API)
            $success = $this->simulateSMSSending($phoneNumber, $message);
            
            if ($success) {
                error_log("SMS sent successfully to: $phoneNumber");
                return true;
            } else {
                error_log("SMS sending failed to: $phoneNumber");
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("SMS sending error: " . $e->getMessage());
            return false;
        }
    }

    public function validatePhoneNumber(string $phoneNumber): bool
    {
        // Remove spaces and special characters
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        // Check if it's a valid format (at least 8 digits)
        return strlen($cleaned) >= 8;
    }
    
    private function simulateSMSSending(string $phoneNumber, string $message): bool
    {
        // For testing - always return true
        // Later replace this with real SMS API call
        
        // Log the SMS details
        error_log("=== SMS SIMULATION ===");
        error_log("To: $phoneNumber");
        error_log("Message: $message");
        error_log("Status: SENT (simulated)");
        error_log("=== END SMS ===");
        
        return true; // Simulate success
    }
}


