<?php

namespace App\Repositories;

use App\Interfaces\IPaymentRepository;
use PDO;

class PaymentRepository implements IPaymentRepository
{
    private $pdo;
    private $table;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function save($payment)
    {
        $this->pdo->beginTransaction();
        try {
            $data = $payment->toArray();
            unset($data['id']); // Remove ID for insert
            
            $columns = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
            $stmt->execute($data);
            
            $id = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            return $id;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}