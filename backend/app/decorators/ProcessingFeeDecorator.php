<?php

namespace App\Decorators;

class ProcessingFeeDecorator extends BasePaymentDecorator
{
    private $processingFee;
    private $description;

    public function __construct($payment, float $processingFee, string $description = "Processing fee")
    {
        parent::__construct($payment);
        $this->processingFee = $processingFee;
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        return $this->payment->getTotalAmount() + $this->processingFee;
    }

    public function getDescription(): string
    {
        $baseDescription = method_exists($this->payment, 'getDescription')
            ? $this->payment->getDescription()
            : 'Payment';
        return $baseDescription . " + {$this->description} (+{$this->processingFee})";
    }

    public function getProcessingFee(): float
    {
        return $this->processingFee;
    }
}