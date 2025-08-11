<?php

namespace App\Controllers;
use App\Services\CourseService;
use App\Models\Course;
use Core\Db;

class CourseController
{
    private $courseService;
    private $pdo;

    // Fix the nullable parameter type
    public function __construct(?CourseService $courseService = null)
    {
        $this->pdo = Db::connection();
        $this->courseService = $courseService ?? new CourseService();
    }


public function listCourses(): array
{
    try {
        $courses = $this->courseService->listCourses();
        // Convert courses to arrays if they are objects
        $data = array_map(fn($course) => $course->toArray(), $courses);

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);

        return ['data' => $data]; // Always return an array
    } catch (\Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');

        $error = ['error' => 'Failed to fetch students', 'message' => $e->getMessage()];
        echo json_encode($error);

        return $error; // Ensure we return something here too
    }
}


    public function getCourseById($id): ?Course
    {
        $course = $this->courseService->getCourseById($id);
        return $course;
    }

    public function createCourse(array $data): ?Course
    {
        $course = $this->courseService->createCourse($data);
        return $course;
    }

    public function deleteCourse($id): bool
    {
        $result = $this->courseService->delete($id);
        return $result;
    }
}
