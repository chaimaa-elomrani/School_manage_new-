<?php

namespace App\Services;

use App\Interfaces\INoteService;
use App\Models\Grades;

class GradeService implements INoteService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Grades $grade): Grades
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO grades (student_id, evaluation_id, score, created_at) 
            VALUES (?, ?, ?, NOW())
        ');
        $stmt->execute([
            $grade->getStudentId(),
            $grade->getEvaluationId(),
            $grade->getScore()
        ]);
        
        $id = $this->pdo->lastInsertId();
        $data = array_merge($grade->toArray(), ['id' => $id]);
        return new Grades($data);
    }

    public function getGradesByStudent($studentId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM grades WHERE student_id = ?
        ');
        $stmt->execute([$studentId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $grades = [];
        foreach ($rows as $row) {
            $grades[] = new Grades($row);
        }
        return $grades;
    }

    public function getGradesByEvaluation($evaluationId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM grades WHERE evaluation_id = ?
        ');
        $stmt->execute([$evaluationId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $grades = [];
        foreach ($rows as $row) {
            $grades[] = new Grades($row);
        }
        return $grades;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT g.*, e.title as evaluation_title, 
                   c.name as course_name,
                   CONCAT(p.first_name, " ", p.last_name) as student_name
            FROM grades g 
            LEFT JOIN evaluations e ON g.evaluation_id = e.id
            LEFT JOIN courses c ON e.subject_id = c.subject_id
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN person p ON s.person_id = p.id
            ORDER BY g.created_at DESC
        '); 
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id): ?Grades
    {
        $stmt = $this->pdo->prepare('SELECT * FROM grades WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? new Grades($row) : null;
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO grades (student_id, evaluation_id, score, created_at) 
            VALUES (?, ?, ?, NOW())
        ');
        $stmt->execute([
            $data['student_id'],
            $data['evaluation_id'],
            $data['score']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update(Grades $grade): Grades
    {
        $stmt = $this->pdo->prepare('
            UPDATE grades 
            SET student_id = ?, evaluation_id = ?, score = ?, updated_at = NOW()
            WHERE id = ?
        ');
        $stmt->execute([
            $grade->getStudentId(),
            $grade->getEvaluationId(),
            $grade->getScore(),
            $grade->getId()
        ]);
        return $grade;
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM grades WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
