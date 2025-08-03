<?php

namespace App\Services;

use App\Models\SalaireEnseignant;
use PDO;

class SalaireEnseignantService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(SalaireEnseignant $salary): SalaireEnseignant
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO salaries (teacher_id, month, year, amount, payment_date, status) 
                VALUES (:teacher_id, :month, :year, :amount, :payment_date, :status)
            ');
            $stmt->execute([
                'teacher_id' => $salary->getTeacherId(),
                'month' => $salary->getMonth(),
                'year' => $salary->getYear(),
                'amount' => $salary->getTotalAmount(),
                'payment_date' => $salary->getPaymentDate(),
                'status' => $salary->getStatus()
            ]);

            $salaryId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            return new SalaireEnseignant([
                'id' => $salaryId,
                'teacher_id' => $salary->getTeacherId(),
                'month' => $salary->getMonth(),
                'year' => $salary->getYear(),
                'amount' => $salary->getTotalAmount(),
                'payment_date' => $salary->getPaymentDate(),
                'status' => $salary->getStatus()
            ]);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM salaries ORDER BY year DESC, month DESC');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $salaries = [];
        foreach ($rows as $row) {
            $salaries[] = new SalaireEnseignant($row);
        }
        return $salaries;
    }

    public function getById($id): ?SalaireEnseignant
    {
        $stmt = $this->pdo->prepare('SELECT * FROM salaries WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new SalaireEnseignant($row) : null;
    }
}