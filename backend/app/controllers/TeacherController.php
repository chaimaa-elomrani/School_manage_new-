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
        $teachers = $this->teacherService->listTeachers();
        return $teachers;
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

    public function delete($id)
    {
        $result = $this->teacherService->delete($id);
        return $result;
    }



}