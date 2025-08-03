<?php

namespace App\Models;

use App\Interfaces\IPaymentCalculator;
use App\Interfaces\IExtraFeeable;
use App\Interfaces\IPaymentStatus;

class SalaireEnseignant implements IPaymentCalculator, IExtraFeeable, IPaymentStatus
{
    private $id;
    private $teacher_id;
    private $month;
    private $year;
    private $amount;
    private $payment_date;
    private $status;
    private $bonus = 0;
    private $deduction = 0;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->teacher_id = $data['teacher_id'] ?? null;
        $this->month = $data['month'] ?? null;
        $this->year = $data['year'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->payment_date = $data['payment_date'] ?? null;
        $this->status = $data['status'] ?? 'pending';
        $this->bonus = $data['bonus'] ?? 0;
        $this->deduction = $data['deduction'] ?? 0;
    }

    // IPaymentCalculator
    public function getBaseAmount(): float
    {
        return (float) $this->amount;
    }

    public function getTotalAmount(): float
    {
        return $this->getBaseAmount() + $this->bonus - $this->deduction;
    }

    // IExtraFeeable (bonus)
    public function applyExtraFee(float $extraFee): void
    {
        $this->bonus = $extraFee;
    }

    public function getExtraFee(): float
    {
        return $this->bonus;
    }

    // Custom deduction method (not discount)
    public function applyDeduction(float $deduction): void
    {
        $this->deduction = $deduction;
    }

    public function getDeduction(): float
    {
        return $this->deduction;
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
    public function getTeacherId() { return $this->teacher_id; }
    public function getMonth() { return $this->month; }
    public function getYear() { return $this->year; }
    public function getPaymentDate() { return $this->payment_date; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'month' => $this->month,
            'year' => $this->year,
            'amount' => $this->amount,
            'bonus' => $this->bonus,
            'deduction' => $this->deduction,
            'total_amount' => $this->getTotalAmount(),
            'payment_date' => $this->payment_date,
            'status' => $this->status
        ];
    }

    public function getDescription(): string
    {
        return "Teacher salary for {$this->month}/{$this->year}";
    }
}
