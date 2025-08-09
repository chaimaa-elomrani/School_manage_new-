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

    public function create(array $data){
        $evaluation = new Evaluation($data);
        $sql = "INSERT INTO evaluations (subject_id, title, teacher_id, date_evaluation) VALUES (:subject_id, :title, :teacher_id, :date_evaluation)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'subject_id' => $evaluation->getSubjectId(),
            'title' => $evaluation->getTitle(),
            'teacher_id' => $evaluation->getTeacherId(),
            'date_evaluation' => $evaluation->getDate(),
        ]);
        return $this->pdo->lastInsertId();
    }


    public function listEvaluations(){
        $sql = "SELECT e.* FROM evaluations e";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluationById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM evaluations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(Evaluation $evaluation){
        $this->pdo->beginTransaction();
        try{
        $sql = "UPDATE evaluations SET subject_id = :subject_id, title = :title, teacher_id = :teacher_id, date_evaluation = :date_evaluation WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $evaluation->getId(),
            'subject_id' => $evaluation->getSubjectId(),
            'title' => $evaluation->getTitle(),
            'teacher_id' => $evaluation->getTeacherId(),
            'date_evaluation' => $evaluation->getDate(),
        ]);
        $this->pdo->commit();
        return $evaluation->getId();
    } catch (\Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
    }

    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM evaluations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }
}