<?php

namespace App\Interfaces;

interface IFinancialService
{
    public function processPayment($payment);
    public function calculateTotalWithTax($amount, $taxRate = 0.0): float;
    public function generatePaymentReport($paymentId): array;
}