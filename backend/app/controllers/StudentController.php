<?php

namespace App\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use App\Factories\PersonFactory; // Assuming this is still needed
use Core\Db;
use PDO;

class StudentController
{
    private $studentService;

    public function __construct(?StudentService $studentService = null)
    {
        if ($studentService === null) {
            $this->studentService = new StudentService();
        } else {
            $this->studentService = $studentService;
        }
    }

    public function listStudents()
    {
        try {
            $students = $this->studentService->listStudents();
            // Convert students to an array of associative arrays if they are objects
            $data = array_map(fn($student) => $student->toArray(), $students);

            header('Content-Type: application/json');
            echo json_encode(['data' => $data]); // Ensure the 'data' key matches frontend expectation
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to fetch students', 'message' => $e->getMessage()]);
        }
    }

    public function getStudentById($id)
    {
        try {
            $student = $this->studentService->getStudentById($id);
            if ($student) {
                header('Content-Type: application/json');
                echo json_encode(['data' => $student->toArray()]);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Student not found']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to fetch student', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->studentService->delete($id);
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Student deleted successfully']);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Student not found or could not be deleted']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to delete student', 'message' => $e->getMessage()]);
        }
    }
}