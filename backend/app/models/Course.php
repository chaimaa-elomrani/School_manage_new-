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
    private $specialty;

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
        $this->specialty = $data['specialty'] ?? null;
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
            'teacher_name' => $this->teacher_name,
            'subject_name' => $this->subject_name,
            'class_number' => $this->class_number,
            'specialty' => $this->specialty
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

    public function getTeacherId()
    {
        return $this->teacher_id;
    }

    public function getClassNumber()
    {
        return $this->class_number;
    }

    public function getSubjectName()
    {
        return $this->subject_name;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getCourseStartDate()
    {
        return $this->start_date;
    }

    public function getCourseEndDate()
    {
        return $this->end_date;
    }

    public function getTeacherName()
    {
        return $this->teacher_name;
    }

    public function getSpecialty()
    {
        return $this->specialty;
    }
}
