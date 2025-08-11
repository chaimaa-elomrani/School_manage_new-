<?php

namespace App\Services;

use App\Models\Subject;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class SubjectService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function create(Subject $subject): Subject
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO subjects (name, description) VALUES (:name, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $subject->getName(),
            ]);
            $subjectId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $subject->toArray();
            $data['id'] = (int) $subjectId;
            return new Subject($data);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getSubjectById(int $id): ?Subject
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subjects WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Subject($row);
        }
        return null;
    }

    public function getAllSubjects(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subjects ORDER BY name');
        $stmt->execute();
        $subjects = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $subjects[] = new Subject($row);
        }
        return $subjects;
    }

    public function update(Subject $subject): Subject
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE subjects SET name = :name, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $subject->getId(),
                'name' => $subject->getName(),
            ]);
            $this->pdo->commit();
            return $subject;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('DELETE FROM subjects WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
