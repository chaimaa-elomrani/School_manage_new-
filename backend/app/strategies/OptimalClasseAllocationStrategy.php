<?php

namespace App\Strategies;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use App\Services\ScheduleService;
use App\Services\ClasseService;
use DateTime;
use Exception;
use PDO; 
class OptimalClasseAllocationStrategy implements SchedulingStrategyInterface
{
    private ScheduleService $scheduleService;
    private ClasseService $ClasseService;
    
    public function __construct(PDO $pdo)
    {
        $this->scheduleService = new ScheduleService();
        $this->ClasseService = new ClasseService($pdo); 
    }
    
    
    public function planCourse(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): Schedule
    {
        // Find the most optimal Classe based on simple criteria (e.g., smallest compatible)
        $optimalClasse = $this->findOptimalClasse($course, $date, $startTime, $endTime);
        
        if (!$optimalClasse) {
            throw new Exception("Aucune salle optimale disponible pour le cours.");
        }
        
        // Now, check for conflicts with the chosen optimal Classe
        $conflicts = $this->getConflicts($course, $optimalClasse, $date, $startTime, $endTime);
        
        if (!empty($conflicts)) {
            throw new Exception("Conflits détectés avec la salle optimale: " . implode(", ", $conflicts));
        }
        
        // If no conflicts, proceed to create the schedule with the optimal Classe
        $scheduleData = [
            'course_id' => $course->getId(),
            'Classe_id' => $optimalClasse->getId(),
            'teacher_id' => $course->getTeacherId(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'planifie'
        ];
        
        $schedule = new Schedule($scheduleData);
        return $this->scheduleService->save($schedule);
    }
    
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
        
        // Check Teacher Conflicts
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
    
    private function findOptimalClasse(Course $course, DateTime $date, string $startTime, string $endTime): ?Classe
{
    // Get all available Classes for the given time slot
    // This method needs to be implemented in your ClasseService
    $availableClasses = $this->ClasseService->getAvailableClasses($date->format('Y-m-d'), $startTime, $endTime);
    
    $bestClasse = null;
    $minCapacityDifference = PHP_INT_MAX;
    
    foreach ($availableClasses as $Classe) {
        // Check if Classe meets minimum capacity requirements
        if ($Classe->getCapacity() >= $course->getRequiredCapacity()) {
            
            $capacityDifference = $Classe->getCapacity() - $course->getRequiredCapacity();
            
            // Simple optimization: find the smallest Classe that fits
            if ($capacityDifference < $minCapacityDifference) {
                $minCapacityDifference = $capacityDifference;
                $bestClasse = $Classe;
            }
        }
    }
    
    return $bestClasse;
}
    
    private function isClasseTypeCompatible(string $requiredType, string $ClasseType): bool
    {
        // Simple compatibility logic:
        // Example: 'lecture' course can be in 'amphitheater' or 'classClasse'
        $compatibilityMap = [
            'lecture' => ['amphitheater', 'classClasse'],
            'lab' => ['laboratory'],
            'seminar' => ['classClasse', 'seminar_Classe'],
            // Add more as needed
        ];
        
        return in_array($ClasseType, $compatibilityMap[$requiredType] ?? []);
    }
    
    private function timeOverlaps(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }
}
