<?php

namespace App\Controllers;

use App\Models\FraisScolaire;
use App\Services\FraisScolaireService;
use Core\Db;

class FraisScolaireController
{
    private $feeService;

    public function __construct()
    {
        $pdo = Db::connection();
        $this->feeService = new FraisScolaireService($pdo);
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $fee = new FraisScolaire($input);
            
            // Apply discount and extra fees if provided
            if (isset($input['discount'])) {
                $fee->applyDiscount($input['discount']);
            }
            if (isset($input['extra_fee'])) {
                $fee->applyExtraFee($input['extra_fee']);
            }

            $result = $this->feeService->save($fee);
            echo json_encode(['message' => 'School fee created successfully', 'data' => $result->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $fees = $this->feeService->getAll();
            $feesArray = [];
            foreach ($fees as $fee) {
                $feesArray[] = $fee->toArray();
            }
            echo json_encode(['message' => 'School fees retrieved successfully', 'data' => $feesArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id)
    {
        try {
            $fee = $this->feeService->getById($id);
            if ($fee) {
                echo json_encode(['message' => 'School fee found', 'data' => $fee->toArray()]);
            } else {
                echo json_encode(['error' => 'School fee not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}