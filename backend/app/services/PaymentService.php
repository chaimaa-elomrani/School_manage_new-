<?php

namespace App\Services;

class PaymentService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    p.*,
                    CONCAT(per.first_name, " ", per.last_name) as student_name,
                    sf.name as fee_name,
                    sf.amount as fee_amount,
                    s.id as student_id
                FROM payments p 
                LEFT JOIN students s ON p.student_id = s.id
                LEFT JOIN person per ON s.person_id = per.id
                LEFT JOIN school_fees sf ON p.fee_id = sf.id
                ORDER BY p.payment_date DESC
            ');
            
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Add debug logging
            error_log('Fetched payments: ' . json_encode($results));
            
            return $results;
        } catch (\PDOException $e) {
            error_log('Payment fetch error: ' . $e->getMessage());
            throw $e;
        }
    }
}