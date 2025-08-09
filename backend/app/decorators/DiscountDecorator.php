<?php

namespace App\Decorators;

use App\Interfaces\IPayable;

class DiscountDecorator extends IPayableDecorator
{
    private float $discountPercentage; // e.g., 0.10 for 10%

    public function __construct(IPayable $payable, float $discountPercentage)
    {
        parent::__construct($payable);
        if ($discountPercentage < 0 || $discountPercentage > 1) {
            throw new \InvalidArgumentException("Discount percentage must be between 0 and 1.");
        }
        $this->discountPercentage = $discountPercentage;
    }

    public function getAmount(): float
    {
        return $this->payable->getAmount() * (1 - $this->discountPercentage);
    }

    public function getDescription(): string
    {
        return $this->payable->getDescription() . " (Réduction de " . ($this->discountPercentage * 100) . "%)";
    }
}
