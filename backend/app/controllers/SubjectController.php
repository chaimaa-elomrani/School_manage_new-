<?php


namespace App\Controllers;

use App\Services\SubjectService;
use Core\Db;

class SubjectController
{
    private $subjectService;
    private $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
        $this->subjectService = new SubjectService($this->pdo);
    }

    public function getAll()
    {
        try {
            $subjects = $this->subjectService->getAll();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $subjects
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}