<?php


namespace App\Services;

use PDO;

class SubjectService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM subjects ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error fetching subjects: " . $e->getMessage());
        }
    }
}