<?php

namespace App\Controllers;

use App\Models\Teacher;
use App\Services\TeacherService;
use App\Factories\PersonFactory;
use Core\Db;

class TeacherController
{
    private $teacherService;

    public function __construct(?TeacherService $teacherService = null)
    {
        if ($teacherService === null) {
            $pdo = Db::connection();
            $this->teacherService = new TeacherService();
        } else {
            $this->teacherService = $teacherService;
        }
    }

    public function listTeachers()
    {
          try {
            $teachers = $this->teacherService->listTeachers();
            // Convert students to an array of associative arrays if they are objects
            $data = array_map(fn($teachers) => $teachers->toArray(), $teachers);

            header('Content-Type: application/json');
            echo json_encode(['data' => $data]); // Ensure the 'data' key matches frontend expectation
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to fetch students', 'message' => $e->getMessage()]);
        }
    }

    public function getTeacherById($id)
    {
        $teacher = $this->teacherService->getTeacherById($id);
        if($teacher === null){
            throw new \Exception("Teacher not found");
        }else{   
         return $teacher;
        }
    }

    // public function delete($id)
    // {
    //     $result = $this->teacherService->delete($id);
    //     return $result;
    // }



}