<?php

namespace App\Interfaces;

interface IPayable 
{
    public function getBaseAmount(): float;
    public function applyDiscount(float $discount): void;
    public function applyExtraFee(float $extraFee): void;
    public function getTotalAmount(): float;
    public function markAsPaid(): void;
    public function getStatus(): string;
}
