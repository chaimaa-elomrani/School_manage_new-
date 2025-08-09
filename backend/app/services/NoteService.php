<?php

namespace App\Services;

use App\Interfaces\INoteService;
use App\Models\Note;
use PDO ;

class NoteService implements INoteService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function add(Note $Note): Note{
        $this->pdo->beginTransaction();
        try{
        $sql = "INSERT INTO notes (student_id, evaluation_id, value) VALUES (:student_id, :evaluation_id, :value)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'student_id' => $Note->getStudentId(),
            'evaluation_id' => $Note->getEvaluationId(),
            'value' => $Note->getValue()
        ]);
        $this->pdo->commit();
        return $Note;
        }catch (\Exception $e){
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Note $grade): Note{
        $this->pdo->beginTransaction();
        try{
        $sql = "UPDATE notes SET student_id = :student_id, evaluation_id = :evaluation_id, value = :value WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $grade->getId(),
            'student_id' => $grade->getStudentId(),
            'evaluation_id' => $grade->getEvaluationId(),
            'value' => $grade->getValue()
        ]);
        $this->pdo->commit();
        return $grade;
        }catch (\Exception $e){
            $this->pdo->rollBack();
            throw $e;
        }
      
    }

    public function delete($id): bool{
        $stmt = $this->pdo->prepare('DELETE FROM notes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }

    public function getGradesByStudent($studentId): array{
        $stmt = $this->pdo->prepare('SELECT n.value FROM notes n WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getGradesByEvaluation($evaluationId): array{
        $stmt = $this->pdo->prepare('SELECT n.value FROM notes n WHERE evaluation_id = :evaluation_id');
        $stmt->execute(['evaluation_id' => $evaluationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
