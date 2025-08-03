<?php

namespace App\Controllers;

use App\Models\Grade;
use Core\Db;

class GradeController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function index()
    {
        try {
            echo "<h3>GRADE CONTROLLER DEBUG</h3>";
            
            $stmt = $this->pdo->query("
                SELECT g.*, 
                       CONCAT(p.first_name, ' ', p.last_name) as student_name,
                       c.title as course_name
                FROM grades g
                LEFT JOIN students s ON g.student_id = s.id
                LEFT JOIN person p ON s.person_id = p.id
                LEFT JOIN courses c ON g.course_id = c.id
                ORDER BY g.id DESC
            ");
            
            $grades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo "<p>Total grades found: " . count($grades) . "</p>";
            echo "<pre>";
            var_dump($grades);
            echo "</pre>";
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $grades
            ]);
            
        } catch (\Exception $e) {
            echo "<p>ERROR: " . $e->getMessage() . "</p>";
            error_log("Grade fetch error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch grades'
            ]);
        }
    }
}
