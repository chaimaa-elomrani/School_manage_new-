<?php

namespace App\Models;

class Classroom
{
    private ?int $id;
    private string $name;
    private string $academicYear;
    private ?int $teacher_id;
    private ?array $students; // Optional: to hold assigned students when fetched

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'];
        $this->academicYear = $data['academic_year'];
        $this->TeacherId = $data['teacher_id'] ?? null;
        $this->students = $data['students'] ?? null; // For hydration with students
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getAcademicYear(): string { return $this->academicYear; }
    public function getTeacherId(): ?int { return $this->teacher_id; }
    public function getStudents(): ?array { return $this->students; }

      // Setters (for updates)
    public function setName(string $name): void { $this->name = $name; }
    public function setAcademicYear(string $academicYear): void { $this->academicYear = $academicYear; }
    public function setTeacherId(?int $mainTeacherId): void { $this->mainTeacherId = $mainTeacherId; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setStudents(array $students): void { $this->students = $students; }


    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'academic_year' => $this->academicYear,
            'teacher_id' => $this->teacher_id,
        ];
        if ($this->students !== null) {
            $data['students'] = array_map(fn($s) => $s->toArray(), $this->students);
        }
        return $data;
    }
}
