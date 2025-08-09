<?php

namespace App\Services;

use App\Models\FraisScolaire;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class FraisScolaireService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
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
                'amount' => $fee->getAmount(),
                'type' => $fee->getType()
            ]);
            $feeId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $fee->toArray();
            $data['id'] = (int) $feeId;
            return new FraisScolaire($data);
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

    public function getById(int $id): ?FraisScolaire
    {
        $stmt = $this->pdo->prepare('SELECT * FROM school_fees WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new FraisScolaire($row) : null;
    }
}
