<?php

namespace App\Services;

use App\Models\Recipient;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class RecipientService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    /**
     * Get recipient details (student or teacher) by their ID.
     * This is a simplified example. In a real system, you'd likely have
     * separate StudentService and TeacherService, and a 'Person' table.
     * For now, we'll assume a 'users' table or similar that holds email/phone.
     */
    public function getRecipientById(int $id): ?Recipient
    {
        // This query assumes a 'users' table with 'id', 'first_name', 'last_name', 'email', 'phone_number'
        // You might need to adjust this based on your actual user/person/student/teacher table structure.
        $sql = "SELECT id, first_name, last_name, email, phone_number FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Recipient($row);
        }
        return null;
    }

    /**
     * Get multiple recipients by their IDs.
     */
    public function getRecipientsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, first_name, last_name, email, phone_number FROM users WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        
        $recipients = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $recipients[] = new Recipient($row);
        }
        return $recipients;
    }
}
