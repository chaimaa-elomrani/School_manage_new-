<?php
namespace App\Services;
use App\Models\Evaluation;
use PDO;

class EvaluationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Evaluation $evaluation)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO evaluations (teacher_id, subject_id, title, type, date_evaluation) VALUES (:teacher_id, :subject_id, :title, :type, :date_evaluation)");
            $stmt->execute([
                'teacher_id' => $evaluation->getTeacherId(),
                'subject_id' => $evaluation->getSubjectId(),
                'title' => $evaluation->getTitle(),
                'type' => $evaluation->getType(),
                'date_evaluation' => $evaluation->getDate()
            ]);
            
            $evaluationId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            // Create new evaluation with ID
            return new Evaluation([
                'id' => $evaluationId,
                'teacher_id' => $evaluation->getTeacherId(),
                'subject_id' => $evaluation->getSubjectId(),
                'title' => $evaluation->getTitle(),
                'type' => $evaluation->getType(),
                'date_evaluation' => $evaluation->getDate()
            ]);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }


    public function getAll()
    {
        $stmt = $this->pdo->prepare(
        'SELECT e.id, e.subject_id , e.teacher_id , e.title , e.type , e.date_evaluation,
        s.name , t.person_id , p.first_name , p.last_name FROM evaluations e
        join subjects s ON e.subject_id = s.id 
        join teachers t ON e.teacher_id = t.id
        join person p ON t.person_id = p.id'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $evaluations = [];
        foreach ($rows as $row) {
            $evaluations[] = new Evaluation($row);
        }
        return $evaluations;
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
        'SELECT e.id, e.subject_id , e.teacher_id , e.title , e.type , e.date_evaluation,
        s.name , t.person_id , p.first_name , p.last_name FROM evaluations e
        join subjects s ON e.subject_id = s.id 
        join teachers t ON e.teacher_id = t.id
        join person p ON t.person_id = p.id WHERE e.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Evaluation($row);
        }
        return null;
    }


    public function update(Evaluation $evaluation)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
            'UPDATE evaluations SET teacher_id = :teacher_id, subject_id = :subject_id, title = :title, type = :type, date_evaluation = :date_evaluation WHERE id = :id');
            $stmt->execute([
                'id' => $evaluation->getId(),
                'teacher_id' => $evaluation->getTeacherId(),
                'subject_id' => $evaluation->getSubjectId(),
                'title' => $evaluation->getTitle(),
                'type' => $evaluation->getType(),
                'date_evaluation' => $evaluation->getDate()
            ]);
            $this->pdo->commit();
            return $evaluation;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM evaluations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }

}