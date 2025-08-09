<?php

namespace App\Models;

use App\Interfaces\IPayable;

class SalaireEnseignant implements IPayable
{
    private ?int $id;
    private int $teacherId;
    private float $amount; // The original amount before decorators
    private int $month;
    private int $year;
    private string $paymentDate; // YYYY-MM-DD
    private string $status; // e.g., 'pending', 'paid'

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->teacherId = $data['teacher_id'];
        $this->amount = (float) $data['amount'];
        $this->month = $data['month'];
        $this->year = $data['year'];
        $this->paymentDate = $data['payment_date'];
        $this->status = $data['status'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTeacherId(): int
    {
        return $this->teacherId;
    }
    public function getMonth(): int
    {
        return $this->month;
    }
    public function getYear(): int
    {
        return $this->year;
    }
    public function getPaymentDate(): string
    {
        return $this->paymentDate;
    }
    public function getStatus(): string
    {
        return $this->status;
    }

    // IPayable method
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return "Salaire de l'enseignant ID {$this->teacherId} pour {$this->month}/{$this->year}";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'teacher_id' => $this->teacherId,
            'amount' => $this->amount,
            'month' => $this->month,
            'year' => $this->year,
            'payment_date' => $this->paymentDate,
            'status' => $this->status,
        ];
    }
}
