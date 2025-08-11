<?php

namespace App\Models;

class Course
{
    private $id;
    private $title;
    private $description;
    private $subject_id;
    private $class_id;
    private $teacher_id;
    private $duration;
    private $start_date;
    private $end_date;

    // Additional fields from JOINs
    private $teacher_name;
    private $subject_name;
    private $class_number;
    private $requiredCapacity; 

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->subject_id = $data['subject_id'] ?? null;
        $this->teacher_id = $data['teacher_id'] ?? null;
        $this->class_id = $data['class_id'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->start_date = $data['start_date'] ?? null;
        $this->end_date = $data['end_date'] ?? null;

        // Additional fields from JOINs
        $this->teacher_name = $data['teacher_name'] ?? null;
        $this->subject_name = $data['subject_name'] ?? null;
        $this->class_number = $data['class_number'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'class_id' => $this->class_id,
            'duration' => $this->duration,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'teacher_name' => $this->teacher_name ?? null,
            'subject_name' => $this->subject_name ?? null,
            'classe_number' => $this->classe_number ?? null
        ];
    }

    public function getId()
    {
        return $this->id;
    }
    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    // Add missing methods for schedule strategy
    public function getTeacherId()
    {
        return $this->teacher_id ?? null;
    }
    public function getClassNumber()
    {
        return $this->class_number ?? null;
    }
    public function getSubjectName()
    {
        return $this->subject_Name ?? null;
    }
    public function getDuration()
    {
        return $this->duration ?? null;
    }
    public function getCourseStartDate()
    {
        return $this->start_date ?? null;
    }
    public function getCourseEndDate()
    {
        return $this->end_date ?? null;
    }

    public function getRequiredCapacity()
    {
        return $this->requiredCapacity ?? null;
    }
}