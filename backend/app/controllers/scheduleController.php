<?php

namespace App\Controllers;

use App\Services\ScheduleManager;
use App\Services\ScheduleService;
use App\Services\CourseService;
use App\Services\ClasseService;
use App\Models\Course;
use App\Models\Classe;
use DateTime;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class ScheduleController
{
    private ScheduleManager $scheduleManager;
    private ScheduleService $scheduleService; // Used for listing schedules directly
    private CourseService $courseService;
    private ClasseService $classeService;
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection(); // Get PDO connection for services
        $this->scheduleService = new ScheduleService();
        $this->courseService = new CourseService();
        $this->classeService = new ClasseService();

        // Inject services into ScheduleManager, which encapsulates the scheduling logic
        $this->scheduleManager = new ScheduleManager(
            $this->scheduleService,
            $this->classeService,
            $this->courseService
        );
    }

    /**
     * Handles planning a new course schedule.
     * This method uses the ScheduleManager to apply the chosen scheduling strategy.
     * Method: POST
     * Body: {
     *   "course_id": 1,
     *   "classe_id": 1,
     *   "date": "2024-11-15",
     *   "start_time": "09:00",
     *   "end_time": "11:00",
     *   "strategy": "conflict" OR "optimal_classe"
     * }
     */
    public function planCourse(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $courseId = $input['course_id'] ?? null;
        $classeId = $input['classe_id'] ?? null;
        $dateStr = $input['date'] ?? null;
        $startTime = $input['start_time'] ?? null;
        $endTime = $input['end_time'] ?? null;
        $strategyName = $input['strategy'] ?? 'conflict'; // Default strategy

        if (!$courseId || !$classeId || !$dateStr || !$startTime || !$endTime) {
            $this->sendJsonResponse(['error' => 'Missing required parameters: course_id, classe_id, date, start_time, end_time.'], 400);
            return;
        }

        try {
            // Fetch models using their respective services
            $course = $this->courseService->getCourseById((int)$courseId);
            $classe = $this->classeService->getClassById((int)$classeId);
            $date = new DateTime($dateStr);

            if (!$course) {
                $this->sendJsonResponse(['error' => 'Course not found.'], 404);
                return;
            }
            if (!$classe) {
                $this->sendJsonResponse(['error' => 'Classe not found.'], 404);
                return;
            }

            // Delegate the core scheduling logic to the ScheduleManager
            $this->scheduleManager->setStrategy($strategyName); // Set the strategy
            $schedule = $this->scheduleManager->scheduleCourse($course, $classe, $date, $startTime, $endTime);

            $this->sendJsonResponse([
                'message' => 'Course scheduled successfully.',
                'schedule' => $schedule->toArray(),
                'strategy_used' => $this->scheduleManager->getStrategyInfo()['name']
            ], 201);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to plan course: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lists all schedules.
     * This method directly uses the ScheduleService for data retrieval.
     * Method: GET
     * URL: /schedules
     */
    public function listSchedules(): void
    {
        try {
            $schedules = $this->scheduleService->getAll();
            $this->sendJsonResponse(['schedules' => $schedules]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve schedules: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gets conflicts for a potential schedule without saving it.
     * This method uses the ScheduleManager to check for conflicts based on the chosen strategy.
     * Method: POST
     * Body: {
     *   "course_id": 1,
     *   "classe_id": 1,
     *   "date": "2024-11-15",
     *   "start_time": "09:00",
     *   "end_time": "11:00",
     *   "strategy": "conflict" OR "optimal_classe"
     * }
     */
    public function getSchedulingConflicts(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $courseId = $input['course_id'] ?? null;
        $classeId = $input['classe_id'] ?? null;
        $dateStr = $input['date'] ?? null;
        $startTime = $input['start_time'] ?? null;
        $endTime = $input['end_time'] ?? null;
        $strategyName = $input['strategy'] ?? 'conflict'; // Default strategy for conflict check

        if (!$courseId || !$classeId || !$dateStr || !$startTime || !$endTime) {
            $this->sendJsonResponse(['error' => 'Missing required parameters for conflict check.'], 400);
            return;
        }

        try {
            // Fetch models using their respective services
            $course = $this->courseService->getCourseById((int)$courseId);
            $classe = $this->classeService->getClassById((int)$classeId);
            $date = new DateTime($dateStr);

            if (!$course) {
                $this->sendJsonResponse(['error' => 'Course not found.'], 404);
                return;
            }
            if (!$classe) {
                $this->sendJsonResponse(['error' => 'Classe not found.'], 404);
                return;
            }

            // Delegate conflict checking logic to the ScheduleManager
            $this->scheduleManager->setStrategy($strategyName); // Set the strategy
            $conflicts = $this->scheduleManager->getSchedulingConflicts($course, $classe, $date, $startTime, $endTime);

            if (empty($conflicts)) {
                $this->sendJsonResponse([
                    'message' => 'No conflicts detected.',
                    'strategy_used' => $this->scheduleManager->getStrategyInfo()['name']
                ], 200);
            } else {
                $this->sendJsonResponse([
                    'message' => 'Conflicts detected.',
                    'conflicts' => $conflicts,
                    'strategy_used' => $this->scheduleManager->getStrategyInfo()['name']
                ], 409); // 409 Conflict status code
            }

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to check conflicts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to send JSON responses.
     */
    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
