<?php

namespace App\Decorators;

use App\Interfaces\IPaymentCalculator;

interface IPaymentDecorator extends IPaymentCalculator
{
    public function getDescription(): string;
}