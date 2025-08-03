<?php

namespace App\Interfaces;

use App\Models\Course;
use App\Models\Room;
use App\Models\Schedule;
use DateTime;

interface IPlanningStrategy 
{
    public function plan(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): Schedule; 
    public function cancelPlan(int $scheduleId): bool;
    public function isAvailable(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): bool;
    public function getConflicts(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): array;
}