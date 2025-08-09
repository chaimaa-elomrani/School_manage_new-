<?php

namespace App\Controllers;

use App\Services\BulletinGeneratorService;
use App\Services\NoteService;
use App\Services\StudentService; 
use App\Services\CourseService; 
use App\Models\Student;
use App\Models\Course;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class BulletinController
{
    private BulletinGeneratorService $bulletinGenerator;
    private StudentService $studentService;
    private CourseService $courseService;
    private PDO $pdo; // For direct DB access if needed, or pass to services

    public function __construct(PDO $pdo )
    {
        $this->pdo = Db::connection(); 
        $noteService = new NoteService($pdo); 
        $this->bulletinGenerator = new BulletinGeneratorService($noteService);
        $this->studentService = new StudentService($pdo); 
        $this->courseService = new CourseService($pdo); 
    }

    /**
     * Handles the request to generate a bulletin for a specific student and course.
     * Example URL: /bulletin/generate?student_id=1&course_id=101
     */
    public function generateBulletin(): void
    {
        $studentId = $_GET['student_id'] ?? null;
        $courseId = $_GET['course_id'] ?? null;

        if (!$studentId || !$courseId) {
            $this->sendJsonResponse(['error' => 'Student ID and Course ID are required.'], 400);
            return;
        }

        try {
            // Fetch Student and Course models
            $student = $this->studentService->getStudentById((int)$studentId);
            $course = $this->courseService->getCourseById((int)$courseId);

            if (!$student) {
                $this->sendJsonResponse(['error' => 'Student not found.'], 404);
                return;
            }
            if (!$course) {
                $this->sendJsonResponse(['error' => 'Course not found.'], 404);
                return;
            }

            // Use the StandardBulletinGenerator service to generate the bulletin
            $bulletin = $this->bulletinGenerator->generateAndSaveBulletin($student, $course);

            $this->sendJsonResponse([
                'message' => 'Bulletin generated successfully.',
                'bulletin' => $bulletin->toArray()
            ], 201);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to generate bulletin: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to send JSON responses.
     */
    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    
   public function getBulletinByStudent($studentId, $courseId): void {
        $bulletin = $this->bulletinGenerator->getBulletinByStudent($studentId, $courseId);
        if ($bulletin) {
            $this->sendJsonResponse(['bulletin' => $bulletin->toArray()], 200);
        } else {
            $this->sendJsonResponse(['error' => 'Bulletin not found.'], 404);
        }
    }


    public function listAll(): void {
        $bulletins = $this->bulletinGenerator->listAll();
        $this->sendJsonResponse(['bulletins' => $bulletins], 200);
    }
}
