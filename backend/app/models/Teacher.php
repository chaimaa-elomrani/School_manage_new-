<?php

namespace App\Models;
use App\Interfaces\IPerson;
use App\Interfaces\ITeacher;

class Teacher implements IPerson, ITeacher{
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

    public function __construct(array $data){
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
    public function getId() { return $this->id; }
    public function getName() { return $this->first_name . ' ' . $this->last_name; }
    public function setName($name) { 
        $parts = explode(' ', $name, 2);
        $this->first_name = $parts[0];
        $this->last_name = $parts[1] ?? '';
    }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getRole() { return $this->role; }

    // ITeacher interface methods (legacy - not in database)
    public function getSubject() { return $this->specialty; }
    public function setSubject($subject) { $this->specialty = $subject; }
    public function getSalary() { return 0; } // Not in schema
    public function setSalary($salary) { } // Not in schema
    public function getAbsence() { return 0; } // Not in schema
    public function setAbsence($absence) { } // Not in schema

    // Additional getters for database fields
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getPhone() { return $this->phone; }
    public function getEmployeeNumber() { return $this->employee_number; }
    public function getSpecialty() { return $this->specialty; }
    public function getPersonId() { return $this->person_id; }

    public function toArray() {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->getName(),
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'employee_number' => $this->employee_number,
            'specialty' => $this->specialty
        ];
    }
}