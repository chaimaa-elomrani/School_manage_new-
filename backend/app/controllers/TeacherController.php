<?php

namespace App\Controllers;

use App\Models\Teacher;
use App\Services\TeacherService;
use App\Factories\PersonFactory;
use Core\Db;

class TeacherController{
    private $teacherService;

    public function __construct(TeacherService $teacherService = null){
        if ($teacherService) {
            $this->teacherService = $teacherService;
        } else {
            $pdo = Db::connection();
            $this->teacherService = new TeacherService($pdo);
        }
    }

    public function create(){
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $teacher = PersonFactory::createPerson($input['role'] ?? 'teacher', $input);
            $result = $this->teacherService->save($teacher);
            echo json_encode(['message' => 'Teacher created successfully', 'data' => $result->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll(){
        try {
            $teachers = $this->teacherService->getAll();
            $teachersArray = [];
            foreach($teachers as $teacher) {
                $teachersArray[] = $teacher->toArray();
            }
            echo json_encode(['message' => 'Teachers retrieved successfully', 'data' => $teachersArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id){
        try {
            $teacher = $this->teacherService->getById($id);
            if ($teacher) {
                echo json_encode(['message' => 'Teacher found', 'data' => $teacher->toArray()]);
            } else {
                echo json_encode(['error' => 'Teacher not found']);
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
            $teacher = PersonFactory::createPerson($input['role'] ?? 'teacher', $input);
            $result = $this->teacherService->update($teacher);
            echo json_encode(['message' => 'Teacher updated successfully', 'data' => $result->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete($id){
        try {
            $this->teacherService->delete($id);
            echo json_encode(['message' => 'Teacher deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}