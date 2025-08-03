<?php

namespace App\Strategies;

use App\Interfaces\IPlanningStrategy;
use App\Models\Course;
use App\Models\Room;
use App\Models\Schedule;
use App\Services\ScheduleService;
use DateTime;
use PDO;

class PlanningStrategy implements IPlanningStrategy
{
    private $scheduleService;

    public function __construct(PDO $pdo)
    {
        $this->scheduleService = new ScheduleService();
    }

    public function plan(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): Schedule
    {
        $conflicts = $this->getConflicts($course, $room, $date, $startTime, $endTime);
        
        if (!empty($conflicts)) {
            throw new \Exception("Conflicts found: " . $this->formatConflicts($conflicts));
        }

        if (!$this->validateBusinessRules($course, $room, $date, $startTime, $endTime)) {
            throw new \Exception("Business rules validation failed");
        }

        $scheduleData = [
            'course_id' => $course->getId(),
            'room_id' => $room->getId(),
            'teacher_id' => $course->getTeacherId(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime
        ];

        $schedule = new Schedule($scheduleData);
        return $this->scheduleService->save($schedule);
    }

    public function cancelPlan(int $scheduleId): bool
    {
        $schedule = $this->scheduleService->getById($scheduleId);
        if (!$schedule) {
            throw new \Exception("Schedule not found");
        }

        $this->scheduleService->delete($scheduleId);
        return true;
    }

    public function isAvailable(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): bool
    {
        $conflicts = $this->getConflicts($course, $room, $date, $startTime, $endTime);
        return empty($conflicts);
    }

    public function getConflicts(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): array
    {
        $conflicts = [];
        
        // Check room conflicts
        $roomConflicts = $this->checkRoomConflicts($room, $date, $startTime, $endTime);
        $conflicts = array_merge($conflicts, $roomConflicts);
        
        // Check teacher conflicts
        $teacherConflicts = $this->checkTeacherConflicts($course->getTeacherId(), $date, $startTime, $endTime);
        $conflicts = array_merge($conflicts, $teacherConflicts);
        
        return $conflicts;
    }

    private function checkRoomConflicts(Room $room, DateTime $date, string $startTime, string $endTime): array
    {
        $existingSchedules = $this->scheduleService->getByRoomAndDate($room->getId(), $date->format('Y-m-d'));
        
        $conflicts = [];
        foreach ($existingSchedules as $schedule) {
            if ($this->timeOverlaps($startTime, $endTime, $schedule->getStartTime(), $schedule->getEndTime())) {
                $conflicts[] = ['type' => 'room', 'schedule' => $schedule];
            }
        }
        
        return $conflicts;
    }

    private function checkTeacherConflicts(int $teacherId, DateTime $date, string $startTime, string $endTime): array
    {
        $existingSchedules = $this->scheduleService->getByTeacherAndDate($teacherId, $date->format('Y-m-d'));
        
        $conflicts = [];
        foreach ($existingSchedules as $schedule) {
            if ($this->timeOverlaps($startTime, $endTime, $schedule->getStartTime(), $schedule->getEndTime())) {
                $conflicts[] = ['type' => 'teacher', 'schedule' => $schedule];
            }
        }
        
        return $conflicts;
    }

    private function validateBusinessRules(Course $course, Room $room, DateTime $date, string $startTime, string $endTime): bool
    {
        // No classes on weekends
        if ($date->format('N') >= 6) {
            return false;
        }
        
        // Classes between 8 AM and 6 PM
        $startHour = (int)date('H', strtotime($startTime));
        $endHour = (int)date('H', strtotime($endTime));
        
        if ($startHour < 8 || $endHour > 18) {
            return false;
        }
        
        // Room level matches course level
        if ($room->getLevel() !== $course->getLevel()) {
            return false;
        }
        
        return true;
    }

    private function timeOverlaps(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }

    private function formatConflicts(array $conflicts): string
    {
        $messages = [];
        foreach ($conflicts as $conflict) {
            $messages[] = ucfirst($conflict['type']) . " conflict at " . $conflict['schedule']->getStartTime();
        }
        return implode(', ', $messages);
    }
}
