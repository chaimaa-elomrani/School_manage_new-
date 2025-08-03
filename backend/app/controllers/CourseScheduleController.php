<?php

namespace App\Controllers;

use App\Services\CourseService;
use App\Strategies\CourseScheduleStrategy;
use Core\Db;

class CourseScheduleController
{
    private $courseService;
    private $courseScheduleStrategy;

    public function __construct()
    {
        $pdo = Db::connection();
        $this->courseService = new CourseService($pdo);
        $this->courseScheduleStrategy = new CourseScheduleStrategy($pdo);
    }

    public function createScheduleForCourse()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $course = $this->courseService->getById($input['course_id']);
            if (!$course) {
                echo json_encode(['error' => 'Course not found']);
                return;
            }

            $schedule = $this->courseScheduleStrategy->createScheduleForCourse($course, $input);
            echo json_encode(['message' => 'Schedule created for course', 'data' => $schedule->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getCourseSchedules($courseId)
    {
        try {
            $schedules = $this->courseScheduleStrategy->getSchedulesForCourse($courseId);
            echo json_encode(['message' => 'Course schedules retrieved', 'data' => array_map(fn($s) => $s->toArray(), $schedules)]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}