<?php

namespace App\Models;
use App\Interfaces\IPerson;
use App\Interfaces\IStudent;
use PDO; 
class Student implements IPerson, IStudent{
    private $id;
    private $person_id;
    private $student_number;
    private $room_id;
    
    // Person fields
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $role;

    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->person_id = $data['person_id'] ?? null;
        $this->student_number = $data['student_number'] ?? '';
        $this->room_id = $data['room_id'] ?? null;
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? 'student';
    }

    // IPerson interface methods
    public function getId() { return $this->id; }
    public function getName() { return $this->first_name . ' ' . $this->last_name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getRoom() { return $this->room_id; }
    public function getStudentNumber() { return $this->student_number; }
    public function getAbsence() { return 0; } // Not in schema, return default
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getPhone() { return $this->phone; }
    public function getPersonId() { return $this->person_id; }

    // Setters
    public function setName($name) { 
        $parts = explode(' ', $name, 2);
        $this->first_name = $parts[0];
        $this->last_name = $parts[1] ?? '';
    }
    public function setEmail($email) { $this->email = $email; }
    public function setRoom($class) { $this->room_id = $class; }
    public function setStudentNumber($studentNumber) { $this->student_number = $studentNumber; }
    public function setAbsence($absence) { } 

    // Additional getters

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
            'student_number' => $this->student_number,
            'class_id' => $this->room_id
        ];
    }


public function save(PDO $pdo)
{
    $stmt = $pdo->prepare('INSERT INTO students (person_id, student_number, room_id) VALUES (?, ?, ?)');
    $stmt->execute([
        $this->person_id,
        $this->student_number,
        $this->room_id
    ]);
}

}