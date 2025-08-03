<?php

namespace App\Services;

use App\Models\PaiementEleve;
use PDO;

class PaiementEleveService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
                'amount' => $payment->getTotalAmount(),
                'payment_date' => $payment->getPaymentDate(),
                'status' => $payment->getStatus()
            ]);

            $paymentId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            return new PaiementEleve([
                'id' => $paymentId,
                'student_id' => $payment->getStudentId(),
                'fee_id' => $payment->getFeeId(),
                'amount' => $payment->getTotalAmount(),
                'payment_date' => $payment->getPaymentDate(),
                'status' => $payment->getStatus()
            ]);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments ORDER BY payment_date DESC');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $payments = [];
        foreach ($rows as $row) {
            $payments[] = new PaiementEleve($row);
        }
        return $payments;
    }

    public function getById($id): ?PaiementEleve
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new PaiementEleve($row) : null;
    }
}