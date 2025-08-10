<?php

namespace App\Controllers;

use App\Services\PaiementEleveService;
use App\Services\SalaireEnseignantService;
use App\Services\FraisScolaireService;
use App\Services\TransactionService;
use App\Services\StudentService;
use App\Services\TeacherService;

use App\Models\PaiementEleve;
use App\Models\SalaireEnseignant;
use App\Models\FraisScolaire;
use App\Models\Student;
use App\Models\Teacher;

use App\Decorators\DiscountDecorator;
use App\Interfaces\IPayable;

use PDO;
use Core\Db;

class FinancialController
{
    private PaiementEleveService $paiementEleveService;
    private SalaireEnseignantService $salaireEnseignantService;
    private FraisScolaireService $fraisScolaireService;
    private TransactionService $transactionService;
    private StudentService $studentService;
    private TeacherService $teacherService;
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
        $this->paiementEleveService = new PaiementEleveService();
        $this->salaireEnseignantService = new SalaireEnseignantService();
        $this->fraisScolaireService = new FraisScolaireService();
        $this->transactionService = new TransactionService();
        $this->studentService = new StudentService();
        $this->teacherService = new TeacherService();
    }

    /**
     * Handles adding a new student payment.
     * Applies a discount if 'discount_percentage' is provided in the request body.
     * Method: POST
     * Body: { "student_id": 1, "fee_id": 1, "base_amount": 150.00, "payment_date": "2024-11-01", "status": "paid", "discount_percentage": 0.10 }
     */
    public function addStudentPayment(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $studentId = $input['student_id'] ?? null;
        $feeId = $input['fee_id'] ?? null;
        $baseAmount = $input['base_amount'] ?? null;
        $paymentDate = $input['payment_date'] ?? date('Y-m-d');
        $status = $input['status'] ?? 'pending';
        $discountPercentage = $input['discount_percentage'] ?? 0;

        if (!$studentId || !$feeId || $baseAmount === null) {
            $this->sendJsonResponse(['error' => 'Student ID, Fee ID, and Base Amount are required.'], 400);
            return;
        }

        try {
            // 1. Create the base payment object (PaiementEleve implements IPayable)
            $paymentData = [
                'student_id' => (int)$studentId,
                'fee_id' => (int)$feeId,
                'amount' => (float)$baseAmount, // This is the initial base amount
                'payment_date' => $paymentDate,
                'status' => $status,
            ];
            $basePayment = new PaiementEleve($paymentData);

            // 2. Apply decorators in the controller (orchestration logic)
            $decoratedPayment = $basePayment; // Start with the base object
            if ($discountPercentage > 0) {
                $decoratedPayment = new DiscountDecorator($decoratedPayment, (float)$discountPercentage);
            }

            // 3. Get the final calculated amount and description from the decorated object
            $finalAmount = $decoratedPayment->getAmount();
            $finalDescription = $decoratedPayment->getDescription();

            // 4. Create a new PaiementEleve model instance with the final calculated amount
            // This is necessary because the service's save method is type-hinted to PaiementEleve,
            // and the decorator itself is not a PaiementEleve.
            $paymentToSave = new PaiementEleve([
                'student_id' => (int)$studentId,
                'fee_id' => (int)$feeId,
                'amount' => $finalAmount, // Use the final calculated amount for persistence
                'payment_date' => $paymentDate,
                'status' => $status,
                // If you want to store the decorated description, you'd need a 'description' field
                // in your 'PaiementEleve' model and 'payments' table.
                // 'description' => $finalDescription,
            ]);

            // 5. Pass the prepared model to the service for persistence
            $savedPayment = $this->paiementEleveService->save($paymentToSave);

            $this->sendJsonResponse([
                'message' => 'Student payment added successfully.',
                'payment' => $savedPayment->toArray(),
                'final_amount_calculated' => $finalAmount,
                'description_from_decorator' => $finalDescription
            ], 201);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to add student payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handles adding a new teacher salary.
     * Applies a discount if 'discount_percentage' is provided in the request body.
     * Method: POST
     * Body: { "teacher_id": 1, "month": 10, "year": 2024, "base_amount": 2000.00, "payment_date": "2024-10-30", "status": "paid", "discount_percentage": 0.05 }
     */
    public function addTeacherSalary(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $teacherId = $input['teacher_id'] ?? null;
        $month = $input['month'] ?? null;
        $year = $input['year'] ?? null;
        $baseAmount = $input['base_amount'] ?? null;
        $paymentDate = $input['payment_date'] ?? date('Y-m-d');
        $status = $input['status'] ?? 'pending';
        $discountPercentage = $input['discount_percentage'] ?? 0;

        if (!$teacherId || !$month || !$year || $baseAmount === null) {
            $this->sendJsonResponse(['error' => 'Teacher ID, Month, Year, and Base Amount are required.'], 400);
            return;
        }

        try {
            // 1. Create the base salary object (SalaireEnseignant implements IPayable)
            $salaryData = [
                'teacher_id' => (int)$teacherId,
                'month' => (int)$month,
                'year' => (int)$year,
                'amount' => (float)$baseAmount, // Initial base amount
                'payment_date' => $paymentDate,
                'status' => $status,
            ];
            $baseSalary = new SalaireEnseignant($salaryData);

            // 2. Apply decorators in the controller
            $decoratedSalary = $baseSalary; // Start with the base object
            if ($discountPercentage > 0) {
                $decoratedSalary = new DiscountDecorator($decoratedSalary, (float)$discountPercentage);
            }

            // 3. Get the final calculated amount and description from the decorated object
            $finalAmount = $decoratedSalary->getAmount();
            $finalDescription = $decoratedSalary->getDescription();

            // 4. Create a new SalaireEnseignant model instance with the final calculated amount
            $salaryToSave = new SalaireEnseignant([
                'teacher_id' => (int)$teacherId,
                'month' => (int)$month,
                'year' => (int)$year,
                'amount' => $finalAmount, // Use the final calculated amount for persistence
                'payment_date' => $paymentDate,
                'status' => $status,
                // If you want to store the decorated description, you'd need a 'description' field
                // in your 'SalaireEnseignant' model and 'salaries' table.
                // 'description' => $finalDescription,
            ]);

            // 5. Pass the prepared model to the service for persistence
            $savedSalary = $this->salaireEnseignantService->save($salaryToSave);

            $this->sendJsonResponse([
                'message' => 'Teacher salary added successfully.',
                'salary' => $savedSalary->toArray(),
                'final_amount_calculated' => $finalAmount,
                'description_from_decorator' => $finalDescription
            ], 201);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to add teacher salary: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lists all student payments.
     * Method: GET
     * URL: /financial/payments
     */
    public function listStudentPayments(): void
    {
        try {
            $payments = $this->paiementEleveService->getAll();
            $this->sendJsonResponse(['payments' => array_map(fn($p) => $p->toArray(), $payments)]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve student payments: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lists all teacher salaries.
     * Method: GET
     * URL: /financial/salaries
     */
    public function listTeacherSalaries(): void
    {
        try {
            $salaries = $this->salaireEnseignantService->getAll();
            $this->sendJsonResponse(['salaries' => array_map(fn($s) => $s->toArray(), $salaries)]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve teacher salaries: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lists all school fees.
     * Method: GET
     * URL: /financial/fees
     */
    public function listSchoolFees(): void
    {
        try {
            $fees = $this->fraisScolaireService->getAll();
            $this->sendJsonResponse(['school_fees' => array_map(fn($f) => $f->toArray(), $fees)]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve school fees: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to send JSON responses.
     */
    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
