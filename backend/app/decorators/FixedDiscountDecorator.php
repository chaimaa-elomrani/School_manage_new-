<?php

namespace App\Decorators;

class FixedDiscountDecorator extends BasePaymentDecorator
{
    private $discountAmount;
    private $description;

    public function __construct($payment, float $discountAmount, string $description = "Fixed discount")
    {
        parent::__construct($payment);
        $this->discountAmount = $discountAmount;
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        $baseTotal = $this->payment->getTotalAmount();
        return max(0, $baseTotal - $this->discountAmount);
    }

    public function getDescription(): string
    {
        return "{$this->description} (+{$this->discountAmount})";
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }
}