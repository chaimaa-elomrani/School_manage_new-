<?php

namespace App\Strategies;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use App\Services\ScheduleService;
use DateTime;
use Exception;

class ConflictResolutionStrategy implements SchedulingStrategyInterface
{
    private ScheduleService $scheduleService;
    
    public function __construct()
    {
        $this->scheduleService = new ScheduleService(); // ScheduleService handles its own PDO connection
    }
    

    
    public function planCourse(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): Schedule
    {
        $conflicts = $this->getConflicts($course, $Classe, $date, $startTime, $endTime);
        
        if (!empty($conflicts)) {
            throw new Exception("Conflits détectés: " . implode(", ", $conflicts));
        }
        
        // If no conflicts, proceed to create the schedule
        $scheduleData = [
            'course_id' => $course->getId(),
            'Classe_id' => $Classe->getId(),
            'teacher_id' => $course->getTeacherId(), 
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
        
        $schedule = new Schedule($scheduleData);
        return $this->scheduleService->save($schedule);
    }
    
    /**
     * Check if a course can be scheduled at the given time without conflicts.
     */
    public function canSchedule(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): bool
    {
        return empty($this->getConflicts($course, $Classe, $date, $startTime, $endTime));
    }
    

    public function getConflicts(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): array
    {
        $conflicts = [];
        
        // Basic Business Rules Check (e.g., Classe capacity)
        if ($Classe->getCapacity() < $course->getRequiredCapacity()) { 
            $conflicts[] = "Capacité de salle insuffisante pour le cours.";
        }
        
        // Check Classe Conflicts
        $ClasseSchedules = $this->scheduleService->getByClasseAndDate($Classe->getId(), $date->format('Y-m-d'));
        foreach ($ClasseSchedules as $existingSchedule) {
            if ($this->timeOverlaps($startTime, $endTime, $existingSchedule->getStartTime(), $existingSchedule->getEndTime())) {
                $conflicts[] = "La salle {$Classe->getNumber()} est déjà occupée."; 
                break;
            }
        }
        
        if ($course->getTeacherId()) {
            $teacherSchedules = $this->scheduleService->getByTeacherAndDate($course->getTeacherId(), $date->format('Y-m-d'));
            foreach ($teacherSchedules as $existingSchedule) {
                if ($this->timeOverlaps($startTime, $endTime, $existingSchedule->getStartTime(), $existingSchedule->getEndTime())) {
                    $conflicts[] = "L'enseignant est déjà occupé.";
                    break;
                }
            }
        }
        
        return $conflicts;
    }
    
    private function timeOverlaps(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }
}
