<?php

namespace App\Services;

use App\Models\Course;
use PDO;

class CourseService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Course $course): array
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO courses (subject_id, teacher_id, room_id, duration, level, start_date, end_date, title) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $course->getSubjectId(),
            $course->getTeacherId(), 
            $course->getRoomId(),
            $course->getDuration(),
            $course->getLevel(),
            $course->getCourseStartDate(),
            $course->getCourseEndDate(),
            $course->getName() // This will be stored as title
        ]);
        
        return ['id' => $this->pdo->lastInsertId(), 'message' => 'Course saved successfully'];
    }

    public function getById(int $id): ?Course
    {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        return $data ? new Course($data) : null;
    }

    public function getAll(): array
    {
        try {
            $sql = "SELECT 
                c.*,
                s.name as subject_name,
                CONCAT(p.first_name, ' ', p.last_name) as teacher_name,
                r.number as room_number
                FROM courses c
                LEFT JOIN subjects s ON c.subject_id = s.id
                LEFT JOIN teachers t ON c.teacher_id = t.id
                LEFT JOIN rooms r ON c.room_id = r.id
                LEFT JOIN person p ON t.person_id = p.id
                ORDER BY c.id DESC";
                
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug log
            error_log("Fetched courses: " . json_encode($courses));
            
            return $courses;
        } catch (\PDOException $e) {
            error_log("Error fetching courses: " . $e->getMessage());
            throw new \Exception("Error fetching courses: " . $e->getMessage());
        }
    }

    public function update(Course $course): Course
    {
        $stmt = $this->pdo->prepare("
            UPDATE courses 
            SET subject_id = ?, teacher_id = ?, room_id = ?, duration = ?, 
                level = ?, start_date = ?, end_date = ?, title = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $course->getSubjectId(),
            $course->getTeacherId(),
            $course->getRoomId(),
            $course->getDuration(),
            $course->getLevel(),
            $course->getCourseStartDate(),
            $course->getCourseEndDate(),
            $course->getName(),
            $course->getId()
        ]);
        
        return $course;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }
}