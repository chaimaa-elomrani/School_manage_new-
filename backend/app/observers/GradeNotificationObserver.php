<?php

namespace App\Observers;

use App\Interfaces\IObserver;
use App\Services\NotificationService;

class GradeNotificationObserver implements IObserver
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function update(string $event, array $data): void
    {
        error_log("Observer triggered - Event: $event, Data: " . json_encode($data));
        
        switch ($event) {
            case 'grade_created':
                $this->notifyGradeCreated($data);
                break;
            case 'bulletin_generated':
                $this->notifyBulletinGenerated($data);
                break;
        }
    }

    private function notifyGradeCreated(array $data): void
    {
        $message = "New grade added: {$data['score']} for evaluation {$data['evaluation_id']}";
        error_log("Sending grade notification: $message");
        $this->notificationService->notifyParentsAndStudent($data['student_id'], $message);
    }

    private function notifyBulletinGenerated(array $data): void
    {
        $message = "New bulletin generated with grade: {$data['grade']}";
        $this->notificationService->notifyParentsAndStudent($data['student_id'], $message);
    }
}

