<?php

namespace App\Interfaces;

use App\Models\Bulletin;

interface IBulletinGenerator
{
    public function generateBulletin($studentId, $courseId, $evaluationId): Bulletin;
    public function calculateGeneralAverage($studentId, $courseId): float;
    public function getStudentGrades($studentId, $courseId): array;
}