<?php

namespace App\Interfaces;

interface IPaymentNotificationService
{
    public function notifyPaymentCreated($paymentData): void;
    public function notifyPaymentCompleted($paymentData): void;
}