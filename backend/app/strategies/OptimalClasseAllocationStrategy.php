<?php

namespace App\Strategies;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use App\Services\ScheduleService; // Dependency
use App\Services\ClasseService;   // Dependency
use DateTime;
use Exception;

class OptimalClasseAllocationStrategy implements SchedulingStrategyInterface
{
    private ScheduleService $scheduleService;
    private ClasseService $classeService;

    public function __construct(ScheduleService $scheduleService, ClasseService $classeService) // Inject services
    {
        $this->scheduleService = $scheduleService;
        $this->classeService = $classeService;
    }

    public function getName(): string
    {
        return "Stratégie d'Allocation Optimale des Classes";
    }

    public function getDescription(): string
    {
        return "Optimise l'allocation des classes en choisissant la plus petite classe disponible et compatible.";
    }

    public function planCourse(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): Schedule
    {
        // Find the most optimal Classe based on simple criteria (e.g., smallest compatible)
        $optimalClasse = $this->findOptimalClasse($course, $date, $startTime, $endTime);

        if (!$optimalClasse) {
            throw new Exception("Aucune classe optimale disponible pour le cours.");
        }

        // Now, check for conflicts with the chosen optimal Classe
        $conflicts = $this->getConflicts($course, $optimalClasse, $date, $startTime, $endTime);

        if (!empty($conflicts)) {
            throw new Exception("Conflits détectés avec la classe optimale: " . implode(", ", $conflicts));
        }

        // If no conflicts, proceed to create the schedule with the optimal Classe
        $scheduleData = [
            'course_id' => $course->getId(),
            'classe_id' => $optimalClasse->getId(), // Use classe_id for consistency
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
            $conflicts[] = "Capacité de classe insuffisante pour le cours.";
        }

        // Check Classe Conflicts
        $classeSchedules = $this->scheduleService->getByClasseAndDate($Classe->getId(), $date->format('Y-m-d'));
        foreach ($classeSchedules as $existingSchedule) {
            if ($this->timeOverlaps($startTime, $endTime, $existingSchedule->getStartTime(), $existingSchedule->getEndTime())) {
                $conflicts[] = "La classe {$Classe->getNumber()} est déjà occupée.";
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
        $availableClasses = $this->classeService->getAvailableClasses($date->format('Y-m-d'), $startTime, $endTime);

        $bestClasse = null;
        $minCapacityDifference = PHP_INT_MAX;

        foreach ($availableClasses as $Classe) {
            // Check if Classe meets minimum capacity and type requirements
            if ($Classe->getCapacity() >= $course->getRequiredCapacity() && $Classe->getType()) {

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
        // Example: 'lecture' course can be in 'amphitheater' or 'classroom'
        $compatibilityMap = [
            'lecture' => ['amphitheater', 'classroom'],
            'lab' => ['laboratory'],
            'seminar' => ['classroom', 'seminar_room'],
            // Add more as needed
        ];

        return in_array($ClasseType, $compatibilityMap[$requiredType] ?? []);
    }

    private function timeOverlaps(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }
}
