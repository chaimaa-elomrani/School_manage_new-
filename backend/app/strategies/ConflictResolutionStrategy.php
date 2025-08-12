<?php

namespace App\Strategies;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use App\Services\ScheduleService; // Dependency
use DateTime;
use Exception;

class ConflictResolutionStrategy implements SchedulingStrategyInterface
{
    private ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService) // Inject ScheduleService
    {
        $this->scheduleService = $scheduleService;
    }

    public function getName(): string
    {
        return "Stratégie de Résolution de Conflits Simple";
    }

    public function getDescription(): string
    {
        return "Planifie un cours et signale les conflits horaires ou de salle.";
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
            'classe_id' => $Classe->getId(), // Use classe_id for consistency
            'teacher_id' => $course->getTeacherId(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'planifie' // Or 'scheduled'
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
        $classeSchedules = $this->scheduleService->getByClasseAndDate($Classe->getId(), $date->format('Y-m-d'));
        foreach ($classeSchedules as $existingSchedule) {
            if ($this->timeOverlaps($startTime, $endTime, $existingSchedule->getStartTime(), $existingSchedule->getEndTime())) {
                $conflicts[] = "La salle {$Classe->getNumber()} est déjà occupée.";
                break;
            }
        }

        // Check Teacher Conflicts (if teacher is assigned to course)
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
