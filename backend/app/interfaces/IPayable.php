<?php

namespace App\Interfaces;

interface IPayable 
{
    public function getAmount(): float;
    public function getDescription(): string;
}
