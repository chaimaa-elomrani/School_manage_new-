<?php

namespace App\Controllers;
use App\Services\CourseService;
use App\Models\Course;
use Core\Db;

class CourseController
{
    private $courseService;
    private $pdo;

    // Fix the nullable parameter type
    public function __construct(?CourseService $courseService = null)
    {
        $this->pdo = Db::connection();
        $this->courseService = $courseService ?? new CourseService($this->pdo);
    }


    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $course = new Course($input);
            $result = $this->courseService->save($course);
            echo json_encode(['message' => 'Course created successfully', 'data' => $result]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function getAll()
    {
        try {
            $courses = $this->courseService->getAll();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            error_log("Course fetch error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function getById($id)
    {
        try {
            $course = $this->courseService->getById($id);
            echo json_encode($course->toArray());
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $input['id'] = $id; // Add the ID to the input data
            $course = new Course($input);
            $results = $this->courseService->update($course);
            echo json_encode(['message' => 'Course updated successfully', 'data' => $results]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function delete($id)
    {
        try {
            $this->courseService->delete($id);
            echo json_encode(['message' => 'Course deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function index()
    {
        try {
            echo "<h3>COURSE CONTROLLER DEBUG</h3>";
            
            $stmt = $this->pdo->query("
                SELECT c.id, c.subject_id, c.teacher_id, c.room_id, 
                       c.duration, c.level, c.start_date, c.end_date, 
                       c.title as name,
                       s.name as subject_name, 
                       CONCAT(p.first_name, ' ', p.last_name) as teacher_name, 
                       r.number as room_number
                FROM courses c 
                LEFT JOIN subjects s ON c.subject_id = s.id
                LEFT JOIN teachers t ON c.teacher_id = t.id
                LEFT JOIN rooms r ON c.room_id = r.id
                LEFT JOIN person p ON t.person_id = p.id
            ");
            
            $courses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo "<p>Total courses found: " . count($courses) . "</p>";
            echo "<pre>";
            var_dump($courses);
            echo "</pre>";
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $courses
            ]);
            
        } catch (\Exception $e) {
            echo "<p>ERROR: " . $e->getMessage() . "</p>";
            error_log("Course fetch error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch courses'
            ]);
        }
    }
}
