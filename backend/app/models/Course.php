<?php

namespace App\Models;

class Course
{
    private $id;
    private $name;
    private $code;
    private $credits;
    private $description;
    private $subject_id;
    private $teacher_id;
    private $room_id;
    private $duration;
    private $level;
    private $start_date;
    private $end_date;
    
    // Additional fields from JOINs
    private $teacher_name;
    private $subject_name;
    private $room_number;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? $data['title'] ?? '';
        $this->code = $data['code'] ?? '';
        $this->credits = $data['credits'] ?? 0;
        $this->description = $data['description'] ?? '';
        $this->subject_id = $data['subject_id'] ?? null;
        $this->teacher_id = $data['teacher_id'] ?? null;
        $this->room_id = $data['room_id'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->level = $data['level'] ?? null;
        $this->start_date = $data['start_date'] ?? null;
        $this->end_date = $data['end_date'] ?? null;
        
        // Additional fields from JOINs
        $this->teacher_name = $data['teacher_name'] ?? null;
        $this->subject_name = $data['subject_name'] ?? null;
        $this->room_number = $data['room_number'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'credits' => $this->credits,
            'description' => $this->description,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'room_id' => $this->room_id,
            'duration' => $this->duration,
            'level' => $this->level,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'teacher_name' => $this->teacher_name ?? null,
            'subject_name' => $this->subject_name ?? null,
            'room_number' => $this->room_number ?? null
        ];
    }

    // Getters and setters...
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getCode() { return $this->code; }

     public function getCredits()
    {
        return $this->credits;
    }

    public function getDescription() { return $this->description; }
    
    // Add missing methods for schedule strategy
    public function getTeacherId() { return $this->teacher_id ?? null; }
    public function getRoomId() { return $this->room_id ?? null; }
    public function getSubjectId() { return $this->subject_id ?? null; }
    public function getDuration() { return $this->duration ?? null; }
    public function getLevel() { return $this->level ?? null; }
    public function getCourseStartDate() { return $this->start_date ?? null; }
    public function getCourseEndDate() { return $this->end_date ?? null; }
}