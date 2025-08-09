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
        $classe = $stmt->fetch(PDO::FETCH_ASSOC);
        return $classe;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM classes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }

 public function getAvailableClasses($date, $startTime, $endTime){
    $sql = "SELECT * FROM classes 
            WHERE id NOT IN (SELECT class_id FROM courses) 
            AND id NOT IN (SELECT Classe_id FROM schedules 
                            WHERE date = :date 
                            AND (start_time BETWEEN :startTime AND :endTime 
                                 OR end_time BETWEEN :startTime AND :endTime 
                                 OR (:startTime BETWEEN start_time AND end_time 
                                      AND :endTime BETWEEN start_time AND end_time)))";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'date' => $date,
        'startTime' => $startTime,
        'endTime' => $endTime
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
