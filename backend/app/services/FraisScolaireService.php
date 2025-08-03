<?php

namespace App\Services;

use App\Models\FraisScolaire;
use PDO;

class FraisScolaireService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(FraisScolaire $fee): FraisScolaire
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO school_fees (name, amount, type) 
                VALUES (:name, :amount, :type)
            ');
            $stmt->execute([
                'name' => $fee->getName(),
                'amount' => $fee->getTotalAmount(),
                'type' => $fee->getType()
            ]);

            $feeId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            return new FraisScolaire([
                'id' => $feeId,
                'name' => $fee->getName(),
                'amount' => $fee->getTotalAmount(),
                'type' => $fee->getType()
            ]);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM school_fees ORDER BY name');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $fees = [];
        foreach ($rows as $row) {
            $fees[] = new FraisScolaire($row);
        }
        return $fees;
    }

    public function getById($id): ?FraisScolaire
    {
        $stmt = $this->pdo->prepare('SELECT * FROM school_fees WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new FraisScolaire($row) : null;
    }
}