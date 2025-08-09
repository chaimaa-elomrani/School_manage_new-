<?php

namespace App\Services;

use App\Models\SalaireEnseignant;
use App\Models\Transaction; // To record transactions
use PDO;
use Core\Db; // Assuming Core\Db for connection

class SalaireEnseignantService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
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
                'amount' => $salary->getAmount(), // Use getAmount() from IPayable
                'payment_date' => $salary->getPaymentDate(),
                'status' => $salary->getStatus()
            ]);
            $salaryId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            // Record the transaction
            $transactionService = new TransactionService(); // DIP: could be injected
            $transactionService->recordTransaction(new Transaction([
                'type' => 'salary',
                'entity_id' => $salary->getTeacherId(),
                'amount' => $salary->getAmount(),
                'description' => $salary->getDescription(),
            ]));

            $data = $salary->toArray();
            $data['id'] = (int) $salaryId;
            return new SalaireEnseignant($data);
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

    public function getById(int $id): ?SalaireEnseignant
    {
        $stmt = $this->pdo->prepare('SELECT * FROM salaries WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new SalaireEnseignant($row) : null;
    }
}
