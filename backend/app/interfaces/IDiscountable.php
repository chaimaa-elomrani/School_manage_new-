<?php

namespace App\Interfaces;

interface IDiscountable
{
    public function applyDiscount(float $discount): void;
    public function getDiscount(): float;
}