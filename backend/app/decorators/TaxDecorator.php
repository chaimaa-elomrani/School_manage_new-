<?php

namespace App\Decorators;

class TaxDecorator extends BasePaymentDecorator
{
    private $taxRate;
    private $description;

    public function __construct($payment, float $taxRate, string $description = "Tax")
    {
        parent::__construct($payment);
        $this->taxRate = $taxRate;
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        $baseTotal = $this->payment->getTotalAmount();
        return $baseTotal * (1 + $this->taxRate / 100);
    }

    public function getDescription(): string
    {
        $baseDescription = method_exists($this->payment, 'getDescription')
            ? $this->payment->getDescription()
            : 'Payment';
        return $baseDescription . " + {$this->description} ({$this->taxRate}%)";
    }

    public function getTaxAmount(): float
    {
        return $this->payment->getTotalAmount() * ($this->taxRate / 100);
    }
}
