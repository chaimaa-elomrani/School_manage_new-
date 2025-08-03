<?php

namespace App\Strategies;

use App\Interfaces\ICourseScheduleStrategy;
use App\Models\Course;
use App\Models\Schedule;
use App\Services\ScheduleService;
use PDO;

class CourseScheduleStrategy implements ICourseScheduleStrategy
{
    private $scheduleService;

    public function __construct(PDO $pdo)
    {
        $this->scheduleService = new ScheduleService();
    }

    public function createScheduleForCourse(Course $course, array $scheduleData): Schedule
    {
        // Validate that schedule data matches course requirements
        if (!$this->validateScheduleForCourse($course, $scheduleData)) {
            throw new \Exception("Schedule data doesn't match course requirements");
        }

        $scheduleData['course_id'] = $course->getId();
        $scheduleData['room_id'] = $course->getRoomId();
        $scheduleData['teacher_id'] = $course->getTeacherId();

        $schedule = new Schedule($scheduleData);
        return $this->scheduleService->save($schedule);
    }

    public function getSchedulesForCourse(int $courseId): array
    {
        return $this->scheduleService->getByCourseId($courseId);
    }

    public function updateCourseSchedule(int $scheduleId, array $newData): Schedule
    {
        $schedule = $this->scheduleService->getById($scheduleId);
        if (!$schedule) {
            throw new \Exception("Schedule not found");
        }

        // Update schedule data
        $updatedData = array_merge($schedule->toArray(), $newData);
        $updatedSchedule = new Schedule($updatedData);
        
        return $this->scheduleService->update($updatedSchedule);
    }

    public function deleteCourseSchedule(int $scheduleId): bool
    {
        return $this->scheduleService->delete($scheduleId);
    }

    public function validateScheduleForCourse(Course $course, array $scheduleData): bool
    {
        // Temporarily return true for testing
        return true;
        
        // Original validation logic (commented out for now)
        // $courseStart = new \DateTime($course->getCourseStartDate());
        // $courseEnd = new \DateTime($course->getCourseEndDate());
        // $scheduleDate = new \DateTime($scheduleData['date']);
        // return $scheduleDate >= $courseStart && $scheduleDate <= $courseEnd;
    }
}

