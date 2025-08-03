<?php

namespace App\Interfaces;

interface IEmailChannel
{
    public function sendEmail(string $to, string $subject, string $body): bool;
    public function validateEmail(string $email): bool;
}
