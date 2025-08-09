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
        $this->courseService = $courseService ?? new CourseService($this->pdo);
    }


    public function listCourses(): array
    {
        $courses = $this->courseService->listCourses();
        return $courses;

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
