<?php

namespace App\Decorators;

class LateFeeDecorator extends BasePaymentDecorator
{
    private $lateFee;
    private $description;

    public function __construct($payment, float $lateFee, string $description = "Late payment fee")
    {
        parent::__construct($payment);
        $this->lateFee = $lateFee;
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        return $this->payment->getTotalAmount() + $this->lateFee;
    }

    /**
     * Returns the description of the late fee, including the fee amount.
     *
     * @return string The description with the late fee appended in parentheses.
     */
    public function getDescription(): string
    {
        return "{$this->description} (+{$this->lateFee})";
    }

    public function getLateFee(): float
    {
        return $this->lateFee;
    }
}
