<?php

namespace App\Controllers;
use App\Services\EvaluationService;
use App\Models\Evaluation;
use Core\Db;

class EvaluationsController
{
    private $evaluationService;

    public function __construct(EvaluationService $evaluationService = null)
    {
        if ($evaluationService) {
            $this->evaluationService = $evaluationService;
        } else {
            $pdo = Db::connection();
            $this->evaluationService = new EvaluationService($pdo);
        }
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $evaluation = new Evaluation($input);
            $result = $this->evaluationService->save($evaluation);
            echo json_encode(['message' => 'Evaluation created successfully', 'data' => $result]);
            return $result;
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $evaluation = new Evaluation($input);
            $result = $this->evaluationService->update($evaluation);
            echo json_encode(['message' => 'Evaluation updated successfully', 'data' => $result]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->evaluationService->delete($id);
            echo json_encode(['message' => 'Evaluation deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $evaluations = $this->evaluationService->getAll();
            $evaluationsArray = [];
            foreach ($evaluations as $evaluation) {
                $evaluationsArray[] = $evaluation->toArray();
            }
            echo json_encode(['message' => 'Evaluations retrieved successfully', 'data' => $evaluationsArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id)
    {
        try {
            $evaluation = $this->evaluationService->getById($id);
            if ($evaluation) {
                echo json_encode(['message' => 'Evaluation found', 'data' => $evaluation->toArray()]);
            } else {
                echo json_encode(['error' => 'Evaluation not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

}