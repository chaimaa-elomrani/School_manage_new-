<?php

namespace App\Models;

class User
{
    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $role;
    private $password;
    // Add properties for role-specific IDs if you want them explicitly
    public $student_id;
    public $teacher_id;
    public $parent_id;

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

    public function getRole()
    {
        return $this->role;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
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

        // Include role-specific IDs if they are set
        if ($this->student_id !== null) {
            $array['student_id'] = $this->student_id;
        }
        if ($this->teacher_id !== null) {
            $array['teacher_id'] = $this->teacher_id;
        }
        if ($this->parent_id !== null) {
            $array['parent_id'] = $this->parent_id;
        }

        return $array;
    }
}
