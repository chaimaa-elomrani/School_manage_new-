<?php

namespace App\Controllers;

use App\Services\BulletinService;
use App\Services\GradeService;
use App\Services\NotificationService;
use App\Observers\GradeNotificationObserver;
use Core\Db;
use PDO;

class BulletinController
{
    private $bulletinService;

    public function __construct(PDO $pdo)
    {
        $pdo = Db::connection();
        $gradeService = new GradeService($pdo);
        $this->bulletinService = new BulletinService( $pdo , $gradeService);
        
        // Attach observer to bulletin generator
        $notificationService = new NotificationService($pdo);
        $observer = new GradeNotificationObserver(notificationService: $notificationService);
        $this->bulletinService->attach($observer);
    }

    public function generate()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $bulletin = $this->bulletinService->generateBulletin(
                $input['student_id'],
                $input['course_id'],
                $input['evaluation_id']
            );
            
            echo json_encode([
                'message' => 'Bulletin generated successfully',
                'data' => $bulletin->toArray()
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}