<?php
namespace App\Models;
use App\Interfaces\ISchedule;

class Schedule implements ISchedule{
    private $id;
    private $course_id;
    private $room_id;
    private $date;
    private $start_time;
    private $end_time;

    public function __construct(array $data){
        $this->id = $data['id'] ?? null;
        $this->course_id = $data['course_id'] ?? null;
        $this->room_id = $data['room_id'] ?? null;
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
    public function getRoomId() { 
        return $this->room_id; 
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
            'room_id' => $this->room_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
    }
}