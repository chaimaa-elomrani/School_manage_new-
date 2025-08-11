<?php

namespace App\Models;

class Subject
{
    private ?int $id;
    private string $name;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'];
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }

    // Setters for update operations
    public function setName(string $name): void { $this->name = $name; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
