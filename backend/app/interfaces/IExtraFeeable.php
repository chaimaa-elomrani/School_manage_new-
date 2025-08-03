<?php

namespace App\Interfaces;

interface IExtraFeeable
{
    public function applyExtraFee(float $extraFee): void;
    public function getExtraFee(): float;
}