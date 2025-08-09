<?php

namespace App\Models;

use App\Interfaces\IPayable;

class PaiementEleve
{
    private ?int $id;
    private int $studentId;
    private int $feeId; 
    private float $amount; 
    private string $paymentDate; 
    private string $status; 


    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->studentId = $data['student_id'];
        $this->feeId = $data['fee_id'];
        $this->amount = (float) $data['amount'];
        $this->paymentDate = $data['payment_date'];
        $this->status = $data['status'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStudentId(): int
    {
        return $this->studentId;
    }
    public function getFeeId(): int
    {
        return $this->feeId;
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
        return "Paiement de frais scolaires pour l'élève ID {$this->studentId} (Frais ID: {$this->feeId})";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'fee_id' => $this->feeId,
            'amount' => $this->amount,
            'payment_date' => $this->paymentDate,
            'status' => $this->status,
        ];
    }
}
