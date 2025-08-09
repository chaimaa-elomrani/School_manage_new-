<?php

namespace App\Models;

class Bulletin
{
    private ?int $id;
    private int $studentId;
    private int $courseId; // Or subjectId, depending on how you group grades for a bulletin
    private float $generalAverage;
    private string $gradeLetter; // A, B, C, etc.
    private string $generationDate;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->studentId = $data['student_id'];
        $this->courseId = $data['course_id'];
        $this->generalAverage = (float) $data['general_average'];
        $this->gradeLetter = $data['grade_letter'];
        $this->generationDate = $data['generation_date'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStudentId(): int
    {
        return $this->studentId;
    }
    public function getCourseId(): int
    {
        return $this->courseId;
    }
    public function getGeneralAverage(): float
    {
        return $this->generalAverage;
    }
    public function getGradeLetter(): string
    {
        return $this->gradeLetter;
    }
    public function getGenerationDate(): string
    {
        return $this->generationDate;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'course_id' => $this->courseId,
            'general_average' => $this->generalAverage,
            'grade_letter' => $this->gradeLetter,
            'generation_date' => $this->generationDate,
        ];
    }
}
