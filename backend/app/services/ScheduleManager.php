<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use App\Strategies\SchedulingStrategyInterface;
use App\Strategies\ConflictResolutionStrategy;
use App\Strategies\OptimalClasseAllocationStrategy;
use DateTime;
use PDO; // Still needed for Db::connection() if not passed to services

class ScheduleManager
{
    private SchedulingStrategyInterface $strategy;
    private ScheduleService $scheduleService;
    private ClasseService $classeService;
    private CourseService $courseService;

    public function __construct(
        ScheduleService $scheduleService,
        ClasseService $classeService,
        CourseService $courseService
    ) {
        $this->scheduleService = $scheduleService;
        $this->classeService = $classeService;
        $this->courseService = $courseService;

        // Default strategy, correctly injected with its dependencies
        $this->strategy = new ConflictResolutionStrategy($this->scheduleService);
    }

    /**
     * Set the scheduling strategy (Strategy Pattern)
     * The strategy itself needs its dependencies (services)
     */
    public function setStrategy(string $strategyName): void
    {
        switch ($strategyName) {
            case 'conflict':
                $this->strategy = new ConflictResolutionStrategy($this->scheduleService);
                break;
            case 'optimal_classe':
                $this->strategy = new OptimalClasseAllocationStrategy($this->scheduleService, $this->classeService);
                break;
            default:
                throw new \InvalidArgumentException("Unknown scheduling strategy: " . $strategyName);
        }
    }

    /**
     * Get the current strategy
     */
    public function getStrategy(): SchedulingStrategyInterface
    {
        return $this->strategy;
    }

    /**
     * Schedule a course using the current strategy
     */
    public function scheduleCourse(Course $course, Classe $classe, DateTime $date, string $startTime, string $endTime): Schedule
    {
        return $this->strategy->planCourse($course, $classe, $date, $startTime, $endTime);
    }

    /**
     * Check if a course can be scheduled
     */
    public function canScheduleCourse(Course $course, Classe $classe, DateTime $date, string $startTime, string $endTime): bool
    {
        return $this->strategy->canSchedule($course, $classe, $date, $startTime, $endTime);
    }

    /**
     * Get conflicts for a potential scheduling
     */
    public function getSchedulingConflicts(Course $course, Classe $classe, DateTime $date, string $startTime, string $endTime): array
    {
        return $this->strategy->getConflicts($course, $classe, $date, $startTime, $endTime);
    }

    /**
     * Schedule multiple courses
     */
    public function scheduleMultipleCourses(array $courseSchedulingData): array
    {
        $results = [];

        foreach ($courseSchedulingData as $data) {
            try {
                $schedule = $this->scheduleCourse(
                    $data['course'],
                    $data['classe'],
                    $data['date'],
                    $data['startTime'],
                    $data['endTime']
                );

                $results[] = [
                    'success' => true,
                    'schedule' => $schedule,
                    'message' => "Course {$data['course']->getTitle()} scheduled successfully"
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'schedule' => null,
                    'message' => $e->getMessage(),
                    'course' => $data['course']
                ];
            }
        }

        return $results;
    }

    /**
     * Get strategy information
     */
    public function getStrategyInfo(): array
    {
        return [
            'name' => $this->strategy->getName(),
            'description' => $this->strategy->getDescription()
        ];
    }
}
