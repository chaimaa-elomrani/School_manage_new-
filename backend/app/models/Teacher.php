<?php

namespace App\Models;
use App\Interfaces\IPerson;
use App\Interfaces\ITeacher;
use PDO;

class Teacher implements IPerson, ITeacher
{
    private $id;
    private $person_id;
    private $employee_number;
    private $specialty;

    // Person fields
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $role;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->person_id = $data['person_id'] ?? null;
        $this->employee_number = $data['employee_number'] ?? '';
        $this->specialty = $data['specialty'] ?? '';

        // Person fields
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? 'teacher';
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

    public function getSpeciality()
    {
        return $this->specialty;
    }


  
  
    public function getEmployeeNumber()
    {
        return $this->employee_number;
    }


    public function toArray()
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'employee_number' => $this->employee_number,
            'specialty' => $this->specialty
        ];
    }

public function save(PDO $pdo)
{
    $stmt = $pdo->prepare('INSERT INTO teachers (person_id, employee_number, specialty) VALUES (?, ?, ?)');
    $stmt->execute([
        $this->person_id,
        $this->employee_number,
        $this->specialty
    ]);
}

}