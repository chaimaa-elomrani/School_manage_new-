<?php

namespace App\Models;

use App\Interfaces\IPerson;
use PDO;
class Parents implements IPerson
{
    private $id;
    private $person_id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $role;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->person_id = $data['person_id'] ?? null;
        // Person fields
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? 'parent';
    }

    // IPerson interface methods
    public function getId()
    {
        return $this->id;
    }
    public function getFirstName()
    {
        return $this->first_name;
    }
    public function getLastName()
    {
        return $this->last_name;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function getPhone()
    {
        return $this->phone;
    }



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
        ];
    }


    public function save(PDO $pdo)
    {
        $stmt = $pdo->prepare('INSERT INTO students (person_id, occupation, relationship_to_student, can_pickup_student, receives_notifications) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $this->person_id,
        ]);
    }

}
