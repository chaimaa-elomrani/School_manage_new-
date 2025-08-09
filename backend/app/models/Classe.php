<?php

namespace App\Models;

class Classe
{
    private $id;
    private $number;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->number = $data['number'] ?? '';

    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,

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

}
