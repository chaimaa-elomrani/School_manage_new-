<?php

namespace App\Services;

use App\Models\PaiementEleve;
use App\Models\Transaction; // To record transactions
use PDO;
use Core\Db; // Assuming Core\Db for connection

class PaiementEleveService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function save(PaiementEleve $payment): PaiementEleve
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO payments (student_id, fee_id, amount, payment_date, status) 
                VALUES (:student_id, :fee_id, :amount, :payment_date, :status)
            ');
            $stmt->execute([
                'student_id' => $payment->getStudentId(),
                'fee_id' => $payment->getFeeId(),
                'amount' => $payment->getAmount(), // Use getAmount() from IPayable
                'payment_date' => $payment->getPaymentDate(),
                'status' => $payment->getStatus()
            ]);
            $paymentId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            // Record the transaction
            $transactionService = new TransactionService(); // DIP: could be injected
            $transactionService->recordTransaction(new Transaction([
                'type' => 'payment',
                'entity_id' => $payment->getStudentId(),
                'amount' => $payment->getAmount(),
                'description' => $payment->getDescription(),
            ]));

            $data = $payment->toArray();
            $data['id'] = (int) $paymentId;
            return new PaiementEleve($data);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM paiements_eleves ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $payments = [];
        foreach ($rows as $row) {
            $payments[] = new PaiementEleve($row);
        }
        return $payments;
    }

    public function getById(int $id): ?PaiementEleve
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new PaiementEleve($row) : null;
    }
}
