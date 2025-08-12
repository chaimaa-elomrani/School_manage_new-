<?php

namespace App\Models;

use App\Interfaces\IPerson;
use PDO; 
class Person implements IPerson
{
    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $role;
    private $password;

    private $student_id; 
    private $teacher_id;
    private $parent_id;
    // Add properties for role-specific IDs if you want them explicitly


    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? 'admin';
        $this->password = $data['password'] ?? '';

        // Assign role-specific IDs if present in data
        $this->student_id = $data['student_id'] ?? null;
        $this->teacher_id = $data['teacher_id'] ?? null;
        $this->parent_id = $data['parent_id'] ?? null;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getPhone(){
        return $this->phone; 
    }

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role
        ];

        return $array;
    }
    
    public function save(PDO $pdo){
        $stmt = $pdo->prepare('INSERT INTO students (first_name, last_name , email, phone ; role) VALUES (?, ?, ?)');
        $stmt->execute([
            $this->first_name,
            $this->last_name,
            $this->email,
            $this->phone,
            $this->role,
        ]);
    }
    
}
