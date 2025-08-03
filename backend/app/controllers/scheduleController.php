<?php

namespace App\Controllers;

use App\Services\ScheduleService;
use Core\Db;

class ScheduleController
{
    private $scheduleService;

    public function __construct()
    {
        $this->scheduleService = new ScheduleService();
    }

    public function index()
    {
        try {
            // Use the service method instead of direct SQL
            $schedules = $this->scheduleService->getAll();
            
            // Add debug logging
            error_log('Fetched schedules: ' . json_encode($schedules));
            
            if (!$schedules) {
                $schedules = [];
            }

            // Ensure consistent date format
            $formattedSchedules = array_map(function($schedule) {
                $schedule['date'] = date('Y-m-d', strtotime($schedule['date']));
                return [
                    'id' => $schedule['id'],
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'course_name' => $schedule['course_name'] ?? 'Unnamed Course',
                    'teacher_name' => $schedule['teacher_name'] ?? 'Unassigned',
                    'teacher_id' => $schedule['teacher_id'] ?? null,
                    'room_number' => $schedule['room_number'] ?? null,
                    'room_id' => $schedule['room_id'] ?? null,
                    'course_id' => $schedule['course_id'] ?? null
                ];
            }, $schedules);
            
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            
            echo json_encode([
                'success' => true,
                'data' => $formattedSchedules
            ]);
            
        } catch (\Exception $e) {
            error_log("Schedule fetch error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch schedules: ' . $e->getMessage()
            ]);
        }
    }
}
