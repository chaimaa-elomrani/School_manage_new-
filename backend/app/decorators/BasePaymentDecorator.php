<?php

namespace App\Decorators;

use App\Interfaces\IPaymentCalculator;

abstract class BasePaymentDecorator implements IPaymentDecorator
{
    protected $payment;

    public function __construct(IPaymentCalculator $payment)
    {
        $this->payment = $payment;
    }

    public function getBaseAmount(): float
    {
        return $this->payment->getBaseAmount();
    }

    public function getTotalAmount(): float
    {
        return $this->payment->getTotalAmount();
    }

    public function getDescription(): string
    {
        return "Base payment";
    }
}