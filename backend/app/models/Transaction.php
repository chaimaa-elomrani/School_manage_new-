<?php

namespace App\Models;

// Represents a financial transaction record
class Transaction
{
    private ?int $id;
    private string $type; // 'payment', 'salary', 'fee_charge'
    private int $entityId; // ID of the related entity (student_id, teacher_id, fee_id)
    private float $amount;
    private string $transactionDate; // YYYY-MM-DD HH:MM:SS
    private string $description;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->type = $data['type'];
        $this->entityId = $data['entity_id'];
        $this->amount = (float) $data['amount'];
        $this->transactionDate = $data['transaction_date'] ?? date('Y-m-d H:i:s');
        $this->description = $data['description'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getEntityId(): int
    {
        return $this->entityId;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }
    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'entity_id' => $this->entityId,
            'amount' => $this->amount,
            'transaction_date' => $this->transactionDate,
            'description' => $this->description,
        ];
    }
}
