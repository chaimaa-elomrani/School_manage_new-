<?php

namespace App\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use App\Factories\PersonFactory;
use Core\Db;

class StudentController
{

    private $studentService;

    public function __construct(?StudentService $studentService = null)
    {
        if ($studentService === null) {
            $pdo = Db::connection();
            $this->studentService = new StudentService($pdo);
        } else {
            $this->studentService = $studentService;
        }
    }


    public function listStudents()
    {
        $students = $this->studentService->listStudents();
        return $students;
    }

    public function getStudentById($id)
    {
        $student = $this->studentService->getStudentById($id);
        return $student;
    }

    public function delete($id)
    {
        $result = $this->studentService->delete($id);
        return $result;
    }

}
