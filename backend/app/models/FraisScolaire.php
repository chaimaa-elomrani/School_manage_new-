<?php

namespace App\Models;

use App\Interfaces\IPaymentCalculator;
use App\Interfaces\IDiscountable;
use App\Interfaces\IExtraFeeable;

class FraisScolaire implements IPaymentCalculator, IDiscountable, IExtraFeeable
{
    private $id;
    private $name;
    private $amount;
    private $type;
    private $discount = 0;
    private $extra_fee = 0;
    private $status = 'active';

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->amount = $data['amount'] ?? 0;
        $this->type = $data['type'] ?? 'other';
        $this->discount = $data['discount'] ?? 0;
        $this->extra_fee = $data['extra_fee'] ?? 0;
        $this->status = $data['status'] ?? 'active';
    }

    // IPaymentCalculator
    public function getBaseAmount(): float
    {
        return (float) $this->amount;
    }

    public function getTotalAmount(): float
    {
        return $this->getBaseAmount() - $this->discount + $this->extra_fee;
    }

    // IDiscountable
    public function applyDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    // IExtraFeeable
    public function applyExtraFee(float $extraFee): void
    {
        $this->extra_fee = $extraFee;
    }

    public function getExtraFee(): float
    {
        return $this->extra_fee;
    }

    // Custom status methods
    public function activate(): void
    {
        $this->status = 'active';
    }

    public function deactivate(): void
    {
        $this->status = 'inactive';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }

    public function getDescription(): string
    {
        return "School fee: {$this->name}";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type,
            'discount' => $this->discount,
            'extra_fee' => $this->extra_fee,
            'total_amount' => $this->getTotalAmount(),
            'status' => $this->status
        ];
    }
}
