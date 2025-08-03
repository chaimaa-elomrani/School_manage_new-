<?php

namespace App\Controllers;

use App\Services\ScheduleService;
use App\Services\CourseService;
use App\Services\RoomService;
use App\Strategies\PlanningStrategy;
use Core\Db;
use DateTime;

class PlanningController
{
    private $scheduleService;
    private $planningStrategy;
    private $courseService;
    private $roomService;

    public function __construct(PlanningStrategy $planningStrategy = null, CourseService $courseService = null, RoomService $roomService = null, ScheduleService $scheduleService = null)
    {
        if ($planningStrategy && $courseService && $roomService && $scheduleService) {
            $this->planningStrategy = $planningStrategy;
            $this->courseService = $courseService;
            $this->roomService = $roomService;
            $this->scheduleService = $scheduleService;
        } else {
            $pdo = Db::connection();
            $this->planningStrategy = new PlanningStrategy($pdo);
            $this->courseService = new CourseService($pdo);
            $this->roomService = new RoomService($pdo);
            $this->scheduleService = new ScheduleService();
        }
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $course = $this->courseService->getById($input['course_id']);
            $room = $this->roomService->getById($input['room_id']);
            
            if (!$course || !$room) {
                echo json_encode(['error' => 'Course or Room not found']);
                return;
            }

            $date = new DateTime($input['date']);
            $startTime = $input['start_time'];
            $endTime = $input['end_time'];

            $schedule = $this->planningStrategy->plan($course, $room, $date, $startTime, $endTime);
            
            echo json_encode([
                'message' => 'Planning created successfully',
                'data' => $schedule->toArray()
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $schedules = $this->scheduleService->getAll();
            $schedulesArray = [];
            foreach ($schedules as $schedule) {
                $schedulesArray[] = $schedule->toArray();
            }
            echo json_encode(['message' => 'Plannings retrieved successfully', 'data' => $schedulesArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id)
    {
        try {
            $schedule = $this->scheduleService->getById($id);
            if ($schedule) {
                echo json_encode(['message' => 'Planning found', 'data' => $schedule->toArray()]);
            } else {
                echo json_encode(['error' => 'Planning not found']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $this->planningStrategy->cancelPlan($id);

            $course = $this->courseService->getById($input['course_id']);
            $room = $this->roomService->getById($input['room_id']);
            
            if (!$course || !$room) {
                echo json_encode(['error' => 'Course or Room not found']);
                return;
            }
            
            $date = new DateTime($input['date']);
            $startTime = $input['start_time'];
            $endTime = $input['end_time'];

            $schedule = $this->planningStrategy->plan($course, $room, $date, $startTime, $endTime);
            
            echo json_encode(['message' => 'Planning updated successfully', 'data' => $schedule->toArray()]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->planningStrategy->cancelPlan($id);
            
            if ($result) {
                echo json_encode(['message' => 'Planning deleted successfully']);
            } else {
                echo json_encode(['error' => 'Failed to delete planning']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}