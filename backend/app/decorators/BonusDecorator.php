<?php

namespace App\Decorators;

class BonusDecorator extends BasePaymentDecorator
{
    private $bonusAmount;
    private $description;

    public function __construct($payment, float $bonusAmount, string $description = "Performance bonus")
    {
        parent::__construct($payment);
        $this->bonusAmount = $bonusAmount;
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        return $this->payment->getTotalAmount() + $this->bonusAmount;
    }

    public function getDescription(): string
    {
        $baseDescription = method_exists($this->payment, 'getDescription')
            ? $this->payment->getDescription()
            : 'Payment';
        return $baseDescription . " + {$this->description} (+{$this->bonusAmount})";
    }

    public function getBonusAmount(): float
    {
        return $this->bonusAmount;
    }
}