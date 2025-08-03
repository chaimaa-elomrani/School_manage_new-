<?php

namespace App\Interfaces;

use App\Models\Course;
use App\Models\Schedule;

interface ICourseScheduleStrategy 
{
    public function createScheduleForCourse(Course $course, array $scheduleData): Schedule;
    public function getSchedulesForCourse(int $courseId): array;
    public function updateCourseSchedule(int $scheduleId, array $newData): Schedule;
    public function deleteCourseSchedule(int $scheduleId): bool;
    public function validateScheduleForCourse(Course $course, array $scheduleData): bool;
}