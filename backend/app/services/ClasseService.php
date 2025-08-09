<?php

namespace App\Services;

use App\Models\Classe;
use PDO;

class ClasseService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Classe $classe){
        $sql = "INSERT INTO classes (number) VALUES (:number)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'number' => $classe->getNumber(),
        ]);
        return $this->pdo->lastInsertId();
    }

    public function listClasses()
    {
        $sql = "SELECT * FROM classes";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM classes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM classes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }
}
