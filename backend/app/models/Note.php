<?php
namespace App\Models;

class Note {
    private $id;
    private $student_id;
    private $evaluation_id;
    private $value ; 


    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->student_id = $data['student_id'] ?? null;
        $this->evaluation_id = $data['evaluation_id'] ?? null;
        $this->value = $data['value'] ?? null;
    }

    public function getId(){
        return $this->id;
    }

    public function getStudentId(){
        return $this->student_id;
    }

    public function getEvaluationId(){
        return $this->evaluation_id;
    }

    public function getValue(){
        return $this->value;
    }

  

    public function toArray() {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'evaluation_id' => $this->evaluation_id,
            'value' => $this->value
        ];
    }

}
