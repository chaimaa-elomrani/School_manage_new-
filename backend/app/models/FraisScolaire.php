<?php

namespace App\Models;

class FraisScolaire
{
    private ?int $id;
    private string $name;
    private float $amount;
    private string $type; // e.g., 'inscription', 'mensuel', 'activite'

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'];
        $this->amount = (float) $data['amount'];
        $this->type = $data['type'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }
    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type,
        ];
    }
}
