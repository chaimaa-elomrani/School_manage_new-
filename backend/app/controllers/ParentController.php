<?php

namespace App\Controllers;

use App\Services\ParentService;
use Core\Db;

class ParentController
{
    private ParentService $parentService;

    public function __construct(?ParentService $parentService = null)
    {
        if ($parentService === null) {
            $pdo = Db::connection();
            $this->parentService = new ParentService($pdo);
        } else {
            $this->parentService = $parentService;
        }
    }

    public function getChildren($parentId)
    {
        try {
            header('Content-Type: application/json');
            $children = $this->parentService->getChildren($parentId);
            echo json_encode(['success' => true, 'data' => $children]);
        } catch (\Exception $e) {
            error_log("Parent children fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch children data']);
        }
    }

    public function getChildGrades($childId)
    {
        try {
            header('Content-Type: application/json');
            $grades = $this->parentService->getChildGrades($childId);
            echo json_encode(['success' => true, 'data' => $grades]);
        } catch (\Exception $e) {
            error_log("Child grades fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch grades']);
        }
    }

    public function getChildSchedule($childId)
    {
        try {
            header('Content-Type: application/json');
            $schedule = $this->parentService->getChildSchedule($childId);
            echo json_encode(['success' => true, 'data' => $schedule]);
        } catch (\Exception $e) {
            error_log("Child schedule fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch schedule']);
        }
    }

    public function getChildStats($childId)
    {
        try {
            header('Content-Type: application/json');
            $stats = $this->parentService->getChildStats($childId);
            echo json_encode(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            error_log("Child stats fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch stats']);
        }
    }

    public function getPayments($parentId)
    {
        try {
            header('Content-Type: application/json');
            $payments = $this->parentService->getPayments($parentId);
            echo json_encode(['success' => true, 'data' => $payments]);
        } catch (\Exception $e) {
            error_log("Parent payments fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch payments']);
        }
    }

    public function getMessages($parentId)
    {
        try {
            header('Content-Type: application/json');
            $messages = $this->parentService->getMessages($parentId);
            echo json_encode(['success' => true, 'data' => $messages]);
        } catch (\Exception $e) {
            error_log("Parent messages fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch messages']);
        }
    }

    public function getAnnouncements($parentId)
    {
        try {
            header('Content-Type: application/json');
            $announcements = $this->parentService->getAnnouncements($parentId);
            echo json_encode(['success' => true, 'data' => $announcements]);
        } catch (\Exception $e) {
            error_log("Parent announcements fetch error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch announcements']);
        }
    }
}
