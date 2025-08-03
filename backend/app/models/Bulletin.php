<?php
namespace App\Models;

class Bulletin{

    private $id ; 
    private $student_id;
    private $course_id;
    private $evaluation_id;
    private $grade;
    private $general_average ; 



    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->student_id = $data['student_id'];
        $this->course_id = $data['course_id'];
        $this->evaluation_id = $data['evaluation_id'];
        $this->grade = $data['grade'];
        $this->general_average = $data['general_average'] ?? null;
    }


    public function getId(){
        return $this->id;
    }

    public function getStudentId(){
        return $this->student_id;
    }

    public function getCourseId(){
        return $this->course_id;
    }

    public function getEvaluationId(){
        return $this->evaluation_id;
    }

    public function getGrade(){
        return $this->grade;
    }

    public function getGeneralAverage(){
        return $this->general_average;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'evaluation_id' => $this->evaluation_id,
            'grade' => $this->grade,
            'general_average' => $this->general_average
        ];
    }

}