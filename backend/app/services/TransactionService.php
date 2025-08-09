<?php

namespace App\Services;

use App\Models\Transaction;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class TransactionService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function recordTransaction(Transaction $transaction): Transaction
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO transactions (type, entity_id, amount, transaction_date, description)
                VALUES (:type, :entity_id, :amount, :transaction_date, :description)
            ');
            $stmt->execute([
                'type' => $transaction->getType(),
                'entity_id' => $transaction->getEntityId(),
                'amount' => $transaction->getAmount(),
                'transaction_date' => $transaction->getTransactionDate(),
                'description' => $transaction->getDescription(),
            ]);
            $transactionId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $transaction->toArray();
            $data['id'] = (int) $transactionId;
            return new Transaction($data);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions ORDER BY transaction_date DESC');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $transactions = [];
        foreach ($rows as $row) {
            $transactions[] = new Transaction($row);
        }
        return $transactions;
    }

    public function getById(int $id): ?Transaction
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Transaction($row) : null;
    }
}
