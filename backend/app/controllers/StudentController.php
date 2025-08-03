<?php

namespace App\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use App\Factories\PersonFactory;
use Core\Db;

class StudentController
{

    private $studentService ; 

    public function __construct(StudentService $studentService = null)
    {
        if ($studentService) {
            $this->studentService = $studentService;
        } else {
            $pdo = Db::connection();
            $this->studentService = new StudentService($pdo);
        }
    }
    

    public function create(){
        // Get data from request body for POST requests
        $input = json_decode(file_get_contents('php://input'), true); //why do we need this  while we have a service method that got the logic of it? answer : because we want to get the data from the request body and we want to decode it from json to an array so that we can use it in our code ,  but does the data comes like json from the services to decode it ? answer: 
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
            //this two lines means that if the input is not valid we will return an error message
        }

        try {
            $student = PersonFactory::createPerson($input['role'] ?? 'student', $input); // this line means that if the role is not set we will set it to student
            $result = $this->studentService->save($student);
            echo json_encode(['message' => 'Student created successfully', 'data' => $result]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }



    public function getAll(){
        try {
            echo "<h3>STUDENT CONTROLLER DEBUG</h3>";
            
            $students = $this->studentService->getAll();
            $studentsArray = [];
            foreach($students as $student) {
                $studentsArray[] = $student->toArray();
            }
            
            echo "<p>Total students found: " . count($studentsArray) . "</p>";
            echo "<pre>";
            var_dump($studentsArray);
            echo "</pre>";
            
            echo json_encode(['message' => 'Students retrieved successfully', 'data' => $studentsArray]);
        } catch (\Exception $e) {
            echo "<p>ERROR: " . $e->getMessage() . "</p>";
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id){
        try {
            $student = $this->studentService->getById($id);
            if ($student) {
                echo json_encode(['message' => 'Student found', 'data' => $student->toArray()]);
            } else {
                echo json_encode(['error' => 'Student not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(){
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $student = PersonFactory::createPerson($input['role'] ?? 'student', $input); // ($data['role'], $data) this means that we are passing the role and the data to the createPerson method
            $result = $this->studentService->update($student); 
            echo json_encode(['message' => 'Student updated successfully', 'data' => $result]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(){
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            echo json_encode(['error' => 'Student ID is required']);
            return;
        }

        try {
            $result = $this->studentService->delete($input['id']);
            echo json_encode(['message' => 'Student deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getStudentData($studentId)
    {
        try {
            header('Content-Type: application/json');
            
            // Get student enrollments
            $enrollmentStmt = $this->pdo->prepare("
                SELECT e.*, c.title as course_name, c.id as course_id
                FROM enrollments e
                LEFT JOIN courses c ON e.course_id = c.id
                WHERE e.student_id = ? AND e.status = 'active'
            ");
            $enrollmentStmt->execute([$studentId]);
            $enrollments = $enrollmentStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get student grades
            $gradeStmt = $this->pdo->prepare("
                SELECT g.*, c.title as course_name
                FROM grades g
                LEFT JOIN courses c ON g.course_id = c.id
                WHERE g.student_id = ?
            ");
            $gradeStmt->execute([$studentId]);
            $grades = $gradeStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'student_id' => $studentId,
                    'enrollments' => $enrollments,
                    'grades' => $grades
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Student data fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch student data'
            ]);
        }
    }
}
