<?php

namespace App\Interfaces;

use App\Models\Grades;

interface INoteService
{
    public function save(Grades $grade): Grades;
    public function getAll(): array;
    public function getById($id): ?Grades;
    public function update(Grades $grade): Grades;
    public function delete($id): bool;
    public function getGradesByStudent($studentId): array;
    public function getGradesByEvaluation($evaluationId): array;
}