<?php

namespace App\Abstracts;

use App\Models\Bulletin;
use App\Interfaces\ISubject;
use App\Interfaces\IObserver;

abstract class AbstractBulletinGenerator implements ISubject
{
    private $observers = [];

    // Template Method
    public final function generateBulletin($studentId, $courseId, $evaluationId): Bulletin
    {
        error_log("Template Method: Starting bulletin generation for student $studentId");
        
        // Step 1: Collect grades
        $grades = $this->collectGrades($studentId, $courseId);
        error_log("Template Method: Collected " . count($grades) . " grades");
        
        // Step 2: Calculate average
        $average = $this->calculateAverage($grades);
        error_log("Template Method: Calculated average: $average");
        
        // Step 3: Determine grade
        $grade = $this->determineGrade($average);
        error_log("Template Method: Determined grade: $grade");
        
        // Step 4: Format bulletin data
        $bulletinData = $this->formatBulletinData($studentId, $courseId, $evaluationId, $grade, $average);
        
        // Step 5: Create bulletin
        $bulletin = $this->createBulletin($bulletinData);
        
        // Step 6: Notify observers
        $this->notify('bulletin_generated', $bulletin->toArray());
        
        return $bulletin;
    }

    // Abstract methods to be implemented by concrete classes
    abstract protected function collectGrades($studentId, $courseId): array;
    abstract protected function calculateAverage(array $grades): float;
    abstract protected function determineGrade(float $average): string;
    
    // Concrete methods with default implementation
    protected function formatBulletinData($studentId, $courseId, $evaluationId, $grade, $average): array
    {
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'evaluation_id' => $evaluationId,
            'grade' => $grade,
            'general_average' => $average
        ];
    }

    protected function createBulletin(array $data): Bulletin
    {
        return new Bulletin($data);
    }

    // Observer pattern methods
    public function attach(IObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(IObserver $observer): void
    {
        $key = array_search($observer, $this->observers);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify(string $event, array $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
}
