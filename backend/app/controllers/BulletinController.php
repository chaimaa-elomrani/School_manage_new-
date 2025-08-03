<?php

namespace App\Controllers;

use App\Services\StandardBulletinGenerator;
use App\Services\GradeService;
use App\Services\NotificationService;
use App\Observers\GradeNotificationObserver;
use Core\Db;

class BulletinController
{
    private $bulletinGenerator;

    public function __construct()
    {
        $pdo = Db::connection();
        $gradeService = new GradeService($pdo);
        $this->bulletinGenerator = new StandardBulletinGenerator($gradeService);
        
        // Attach observer to bulletin generator
        $notificationService = new NotificationService($pdo);
        $observer = new GradeNotificationObserver($notificationService);
        $this->bulletinGenerator->attach($observer);
    }

    public function generate()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $bulletin = $this->bulletinGenerator->generateBulletin(
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