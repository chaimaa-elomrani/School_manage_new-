<?php

namespace App\Controllers;

use App\Models\Enrollment;
use Core\Db;  // Changed from App\Services\Db to Core\Db

class EnrollmentController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function index()
    {
        try {
            echo "<h3>ENROLLMENT CONTROLLER DEBUG</h3>";
            
            $stmt = $this->pdo->query("
                SELECT e.id, e.student_id, e.course_id, e.status, e.enrollment_date,
                       CONCAT(p.first_name, ' ', p.last_name) as student_name,
                       c.title as course_name
                FROM enrollments e
                LEFT JOIN students s ON e.student_id = s.id
                LEFT JOIN person p ON s.person_id = p.id
                LEFT JOIN courses c ON e.course_id = c.id
                ORDER BY e.enrollment_date DESC
            ");
            
            $enrollments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo "<p>Total enrollments found: " . count($enrollments) . "</p>";
            echo "<pre>";
            var_dump($enrollments);
            echo "</pre>";
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $enrollments
            ]);
            
        } catch (\Exception $e) {
            echo "<p>ERROR: " . $e->getMessage() . "</p>";
            error_log("Enrollment fetch error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch enrollments'
            ]);
        }
    }
}



