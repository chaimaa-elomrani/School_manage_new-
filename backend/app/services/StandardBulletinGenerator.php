<?php

namespace App\Services;

use App\Abstracts\AbstractBulletinGenerator;
use App\Interfaces\INoteService;

class StandardBulletinGenerator extends AbstractBulletinGenerator
{
    private $noteService;

    public function __construct(INoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    protected function collectGrades($studentId, $courseId): array
    {
        return $this->noteService->getGradesByStudent($studentId);
    }

    protected function calculateAverage(array $grades): float
    {
        if (empty($grades)) {
            return 0.0;
        }

        $total = 0;
        foreach ($grades as $grade) {
            $total += $grade->getScore();
        }

        return $total / count($grades);
    }

    protected function determineGrade(float $average): string
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'F';
    }
}