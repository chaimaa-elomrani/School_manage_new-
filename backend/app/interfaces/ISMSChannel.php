<?php

namespace App\Interfaces;

interface ISMSChannel
{
    public function sendSMS(string $phoneNumber, string $message): bool;
    public function validatePhoneNumber(string $phoneNumber): bool;
}


