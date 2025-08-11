<?php

namespace App\Strategies;

use App\Models\Course;
use App\Models\Classe;
use App\Models\Schedule;
use DateTime;

interface SchedulingStrategyInterface
{
    /**
     * Plan a course in a Classe at a specific time.
     * Throws an Exception if planning is not possible due to conflicts or business rules.
     */
    public function planCourse(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): Schedule;

    /**
     * Check if a course can be scheduled at the given time without conflicts.
     */
    public function canSchedule(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): bool;

    /**
     * Get conflicts for a potential scheduling.
     * Returns an array of conflict messages.
     */
    public function getConflicts(Course $course, Classe $Classe, DateTime $date, string $startTime, string $endTime): array;

    /**
     * Get the strategy name.
     */
    public function getName(): string;

    /**
     * Get the strategy description.
     */
    public function getDescription(): string;
}
