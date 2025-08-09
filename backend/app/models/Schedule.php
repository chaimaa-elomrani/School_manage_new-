<?php
namespace App\Models;

class Schedule {
    private $id;
    private $course_id;
    private $Classe_id;
    private $date;
    private $start_time;
    private $end_time;

    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->course_id = $data['course_id'] ?? null;
        $this->Classe_id = $data['Classe_id'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->start_time = $data['start_time'] ?? null;
        $this->end_time = $data['end_time'] ?? null;
    }

    public function getId() { 
        return $this->id; 
    }
    public function getCourseId() { 
        return $this->course_id; 
    }
    public function getClasseId() { 
        return $this->Classe_id; 
    }
    public function getTeacherId() { 
        return null; // Will get from course join
    }
    public function getDate() { 
        return $this->date; 
    }
    public function getStartTime() { 
        return $this->start_time; 
    }
    public function getEndTime() { 
        return $this->end_time; 
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'Classe_id' => $this->Classe_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
    }
}