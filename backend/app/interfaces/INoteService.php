<?php

namespace App\Interfaces;

use App\Models\Note;

interface INoteService
{
    public function add(Note $Note): Note;
    public function update(Note $grade): Note;  
    public function delete($id): bool;
    public function getGradesByStudent($studentId): array;
    public function getGradesByEvaluation($evaluationId): array;
}