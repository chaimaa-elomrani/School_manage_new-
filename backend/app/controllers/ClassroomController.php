<?php

namespace App\Controllers;

use App\Services\ClassroomService; 

class ClassroomController{

    private $classroomService;
    public function __construct()
    {
        $this->classroomService = new ClassroomService();
    }


    public function createClassroom($data){
        $classroom = $this->classroomService->create($data);
        return $classroom;
    }

    public function listClassrooms(){
        $classrooms = $this->classroomService->getAll();
        return $classrooms;
    }

    public function  getById($id){
        $classroom = $this->classroomService->getById($id);
        return $classroom;
    }

    public function update($data){
        $classroom = $this->classroomService->update($data);
        return $classroom;
    }

    public function delete($id){
        $classroom = $this->classroomService->delete($id);
        return $classroom;
    }


    public function assignStudent($classroomId, $studentId){
        $classroom = $this->classroomService->assignStudent($classroomId, $studentId);
        return $classroom;
    }

    public function assignTeacher($classroomId, $teacherId){
        $classroom = $this->classroomService->assignTeacher($classroomId, $teacherId);
        return $classroom;
    }

    public function unassignTeacher($classroomId,  $teacherId){
        $classroom = $this->classroomService->unassignTeacher($classroomId);
        return $classroom;
    }

    public function unassignStudent($classroomId, $studentId){
        $classroom = $this->classroomService->unassignStudent($classroomId, $studentId);
        return $classroom;
    }

    public function getStudentsByClassroom($classroomId){
        $students = $this->classroomService->getStudentsByClassroom($classroomId);
        return $students;
    }

    public function getTeacherByClassroom($classroomId){
        $teacher = $this->classroomService->getTeacherByClassroom($classroomId);
        return $teacher;
    }
}