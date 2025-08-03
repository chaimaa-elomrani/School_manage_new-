<?php

namespace App\Services;

class EnrollmentService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare('
            SELECT se.*, 
                   c.name as course_name,
                   CONCAT(p.first_name, " ", p.last_name) as student_name
            FROM student_enrollments se
            LEFT JOIN courses c ON se.course_id = c.id
            LEFT JOIN students s ON se.student_id = s.id
            LEFT JOIN person p ON s.person_id = p.id
            ORDER BY se.enrollment_date DESC
        '); 
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}