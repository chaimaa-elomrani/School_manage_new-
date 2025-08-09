<?php

namespace App\Models;

class Classe
{
    private $id;
    private $number;
    private $capacity;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->number = $data['number'] ?? '';
        $this->capacity = $data['capacity'] ?? '';

    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'capacity' => $this->capacity
        ];
    }

    // Getters...
    public function getId()
    {
        return $this->id;
    }
    public function getNumber()
    {
        return $this->number;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

}
