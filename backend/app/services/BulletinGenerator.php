<?php

namespace App\Services;

use App\Interfaces\IBulletinGenerator;
use App\Interfaces\INoteService;
use App\Models\Bulletin;

class BulletinGenerator implements IBulletinGenerator
{
    private $noteService;

    public function __construct(INoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function generateBulletin($studentId, $courseId, $evaluationId): Bulletin
    {
        $grades = $this->getStudentGrades($studentId, $courseId);
        $generalAverage = $this->calculateGeneralAverage($studentId, $courseId);
        
        // Logic to determine grade based on average
        $grade = $this->determineGrade($generalAverage);

        return new Bulletin([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'evaluation_id' => $evaluationId,
            'grade' => $grade,
            'general_average' => $generalAverage
        ]);
    }

    public function calculateGeneralAverage($studentId, $courseId): float
    {
        $grades = $this->noteService->getGradesByStudent($studentId);
        // Calculate average logic here
        return 0.0; // Placeholder
    }

    public function getStudentGrades($studentId, $courseId): array
    {
        return $this->noteService->getGradesByStudent($studentId);
    }

    private function determineGrade($average): string
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'F';
    }
}