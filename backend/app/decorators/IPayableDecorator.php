<?php

namespace App\Decorators;

use App\Interfaces\IPayable;

/**
 * Abstract Decorator for IPayable.
 * All concrete decorators will extend this.
 */
abstract class IPayableDecorator implements IPayable
{
    protected IPayable $payable;

    public function __construct(IPayable $payable)
    {
        $this->payable = $payable;
    }

    // Delegate methods to the wrapped IPayable object by default
    public function getAmount(): float
    {
        return $this->payable->getAmount();
    }

    public function getDescription(): string
    {
        return $this->payable->getDescription();
    }
}
