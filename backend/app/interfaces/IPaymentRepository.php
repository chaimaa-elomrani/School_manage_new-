<?php

namespace App\Interfaces;

interface IPaymentRepository
{
    public function save($payment);
    public function getById($id);
    public function getAll(): array;
    public function delete($id): bool;
}