<?php

namespace App\Models;

use App\Interfaces\IPerson;
use PDO;
class Parents implements IPerson
{
    private $id;
    private $person_id;
    private $occupation;
    private $relationship_to_student;
    private $can_pickup_student;
    private $receives_notifications;
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
        $this->occupation = $data['occupation'] ?? null;
        $this->workplace = $data['workplace'] ?? null;
        $this->emergency_contact = $data['emergency_contact'] ?? null;
        $this->relationship_to_student = $data['relationship_to_student'] ?? 'guardian';
        $this->is_primary_contact = $data['is_primary_contact'] ?? false;
        $this->can_pickup_student = $data['can_pickup_student'] ?? true;
        $this->receives_notifications = $data['receives_notifications'] ?? true;
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

    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setName($name)
    {
        $parts = explode(' ', $name, 2);
        $this->first_name = $parts[0];
        $this->last_name = $parts[1] ?? '';
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getRole()
    {
        return $this->role;
    }

    // Additional getters for database fields
    public function getPersonId() { return $this->person_id; }
    public function getOccupation() { return $this->occupation; }
    public function getRelationshipToStudent() { return $this->relationship_to_student; }
        public function getCanPickupStudent() { return $this->can_pickup_student; }
    public function getReceivesNotifications() { return $this->receives_notifications; }


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
            'occupation' => $this->occupation,
            'relationship_to_student' => $this->relationship_to_student,
            'can_pickup_student' => $this->can_pickup_student,
            'receives_notifications' => $this->receives_notifications,
        ];
    }


public function save(PDO $pdo)
{
    $stmt = $pdo->prepare('INSERT INTO students (person_id, occupation, relationship_to_student, can_pickup_student, receives_notifications) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $this->person_id,
        $this->occupation,
        $this->relationship_to_student,
        $this->can_pickup_student,
        $this->receives_notifications
    ]);
}

}
