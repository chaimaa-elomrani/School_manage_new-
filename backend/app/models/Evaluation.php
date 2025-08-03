<?php
namespace App\Models;

class Evaluation{
    
    private $id;
    private $subject_id;
    private $teacher_id;
    private $title;
    private $type;
    private $date_evaluation;

    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->subject_id = $data['subject_id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->teacher_id = $data['teacher_id'] ?? null;
        $this->date_evaluation = $data['date_evaluation'] ?? null;
    }

    public function getId(){
        return $this->id;
    }

    public function getSubjectId(){
        return $this->subject_id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getType(){
        return $this->type;
    }

    public function getDate(){
        return $this->date_evaluation;
    }

    public function getTeacherId(){
        return $this->teacher_id;
    }

    public function setId($id) {
        $this->id = (int)$id; // Ensure it's an integer
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'title' => $this->title,
            'type' => $this->type,
            'date_evaluation' => $this->date_evaluation
        ];
    }
}