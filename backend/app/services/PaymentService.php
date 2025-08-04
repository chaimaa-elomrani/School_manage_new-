<?php
    

namespace App\Services;

class PaymentService
{
    private $pdo;

    public function __construct($pdo)
    {
        if (!$pdo) {
            throw new \Exception('Database connection is required');
        }
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        try {
            // First verify the table exists
            $tableCheck = $this->pdo->query("SHOW TABLES LIKE 'payments'");
            if ($tableCheck->rowCount() === 0) {
                throw new \Exception('Payments table does not exist');
            }

            // Prepare the query with error handling
            $stmt = $this->pdo->prepare('
                SELECT 
                    p.*,
                    CONCAT(COALESCE(per.first_name, ""), " ", COALESCE(per.last_name, "")) as student_name,
                    COALESCE(sf.name, "Unnamed Fee") as fee_name
                FROM payments p 
                LEFT JOIN students s ON p.student_id = s.id
                LEFT JOIN person per ON s.person_id = per.id
                LEFT JOIN school_fees sf ON p.fee_id = sf.id
                ORDER BY p.payment_date DESC
            ');
            
            if (!$stmt) {
                throw new \Exception('Failed to prepare payment query');
            }

            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if ($results === false) {
                throw new \Exception('Failed to fetch payment data');
            }

            // Transform and validate each payment record
            return array_map(function($payment) {
                return [
                    'id' => (int)$payment['id'],
                    'student_id' => (int)$payment['student_id'],
                    'student_name' => $payment['student_name'] ?: 'Unknown Student',
                    'amount' => (float)$payment['amount'],
                    'status' => strtolower($payment['status'] ?? 'pending'),
                    'payment_date' => $payment['payment_date'] ?? null,
                    'due_date' => $payment['due_date'] ?? null,
                    'fee_name' => $payment['fee_name'] ?? 'General Fee'
                ];
            }, $results);

        } catch (\PDOException $e) {
            error_log('Database error in PaymentService: ' . $e->getMessage());
            throw new \Exception('Database error occurred while fetching payments');
        } catch (\Exception $e) {
            error_log('Error in PaymentService: ' . $e->getMessage());
            throw $e;
        }
    }
}
