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


    public function create(array $data){
        $evaluation = new Evaluation($data);
        $result = $this->evaluationService->create($evaluation->toArray());
        return $result;
    }

    public function listEvaluations(){
        $evaluations = $this->evaluationService->listEvaluations();
        return $evaluations;
    }


    public function getEvaluationById($id){
        $evaluation = $this->evaluationService->getEvaluationById($id);
        return $evaluation;
    }

    public function updateEvaluation(Evaluation $evaluation){
        $result = $this->evaluationService->update($evaluation);
        return $result;
    }

    public function deleteEvaluation($id){
        $result = $this->evaluationService->delete($id);
        return $result;
    }


}