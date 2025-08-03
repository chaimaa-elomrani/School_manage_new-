<?php

namespace App\Models;

use App\Interfaces\IPaymentCalculator;
use App\Interfaces\IDiscountable;
use App\Interfaces\IExtraFeeable;
use App\Interfaces\IPaymentStatus;

class PaiementEleve implements IPaymentCalculator, IDiscountable, IExtraFeeable, IPaymentStatus
{
    private $id;
    private $student_id;
    private $fee_id;
    private $amount;
    private $payment_date;
    private $status;
    private $discount = 0;
    private $extra_fee = 0;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->student_id = $data['student_id'] ?? null;
        $this->fee_id = $data['fee_id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->payment_date = $data['payment_date'] ?? null;
        $this->status = $data['status'] ?? 'pending';
        $this->discount = $data['discount'] ?? 0;
        $this->extra_fee = $data['extra_fee'] ?? 0;
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

    // IPaymentStatus
    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->payment_date = date('Y-m-d');
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getStudentId() { return $this->student_id; }
    public function getFeeId() { return $this->fee_id; }
    public function getPaymentDate() { return $this->payment_date; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'fee_id' => $this->fee_id,
            'amount' => $this->amount,
            'discount' => $this->discount,
            'extra_fee' => $this->extra_fee,
            'total_amount' => $this->getTotalAmount(),
            'payment_date' => $this->payment_date,
            'status' => $this->status
        ];
    }

    public function getDescription(): string
    {
        return "Student payment for fee ID: {$this->fee_id}";
    }
}
