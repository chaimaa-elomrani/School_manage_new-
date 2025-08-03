<?php

namespace App\Interfaces;

interface IObserver
{
    public function update(string $event, array $data): void;
}