<?php

namespace App\Controllers;

use App\Models\SalaireEnseignant;
use App\Services\SalaireEnseignantService;
use Core\Db;

class SalaireEnseignantController
{
    private $salaryService;

    public function __construct()
    {
        $pdo = Db::connection();
        $this->salaryService = new SalaireEnseignantService($pdo);
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $salary = new SalaireEnseignant($input);
            
            // Apply bonus and deductions if provided
            if (isset($input['bonus'])) {
                $salary->applyExtraFee($input['bonus']);
            }
            if (isset($input['deduction'])) {
                $salary->applyDeduction($input['deduction']);
            }

            $result = $this->salaryService->save($salary);
            echo json_encode(['message' => 'Salary created successfully', 'data' => $result->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function markAsPaid($id)
    {
        try {
            $salary = $this->salaryService->getById($id);
            if (!$salary) {
                echo json_encode(['error' => 'Salary not found']);
                return;
            }

            $salary->markAsPaid();
            $result = $this->salaryService->save($salary);
            echo json_encode(['message' => 'Salary marked as paid', 'data' => $result->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $salaries = $this->salaryService->getAll();
            $salariesArray = [];
            foreach ($salaries as $salary) {
                $salariesArray[] = $salary->toArray();
            }
            echo json_encode(['message' => 'Salaries retrieved successfully', 'data' => $salariesArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id)
    {
        try {
            $salary = $this->salaryService->getById($id);
            if ($salary) {
                echo json_encode(['message' => 'Salary found', 'data' => $salary->toArray()]);
            } else {
                echo json_encode(['error' => 'Salary not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
