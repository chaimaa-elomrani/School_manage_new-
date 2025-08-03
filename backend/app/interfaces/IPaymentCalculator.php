<?php

namespace App\Interfaces;

interface IPaymentCalculator
{
    public function getTotalAmount(): float;
    public function getBaseAmount(): float;
    public function getDescription(): string;
}
