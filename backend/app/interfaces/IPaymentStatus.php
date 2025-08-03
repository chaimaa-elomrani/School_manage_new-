<?php

namespace App\Interfaces;

interface IPaymentStatus
{
    public function markAsPaid(): void;
    public function getStatus(): string;
}