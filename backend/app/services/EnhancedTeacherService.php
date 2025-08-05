<?php

namespace App\Services;

use PDO;

class EnhancedTeacherService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTeacherCourses($teacherId)
    {
        $sql = "SELECT c.*, s.name as subject_name,
                       COUNT(DISTINCT e.student_id) as student_count
                FROM courses c
                JOIN subjects s ON c.subject_id = s.id
                LEFT JOIN enrollments e ON c.id = e.course_id
                WHERE c.teacher_id = ?
                GROUP BY c.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherSchedule($teacherId)
    {
        $sql = "SELECT sc.*, c.name as course_name, r.number as room_number
                FROM schedules sc
                JOIN courses c ON sc.course_id = c.id
                JOIN rooms r ON sc.room_id = r.id
                WHERE c.teacher_id = ?
                ORDER BY sc.day_of_week, sc.start_time";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherStudents($teacherId)
    {
        $sql = "SELECT DISTINCT s.*, p.first_name, p.last_name,
                       AVG(g.note) as average_grade
                FROM students s
                JOIN person p ON s.person_id = p.id
                JOIN enrollments e ON s.id = e.student_id
                JOIN courses c ON e.course_id = c.id
                LEFT JOIN grades g ON s.id = g.student_id
                LEFT JOIN evaluations ev ON g.evaluation_id = ev.id AND ev.course_id = c.id
                WHERE c.teacher_id = ?
                GROUP BY s.id, p.first_name, p.last_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherGrades($teacherId)
    {
        $sql = "SELECT g.*, CONCAT(p.first_name, ' ', p.last_name) as student_name,
                       c.name as course_name, e.type as evaluation_type, e.date
                FROM grades g
                JOIN evaluations e ON g.evaluation_id = e.id
                JOIN courses c ON e.course_id = c.id
                JOIN students s ON g.student_id = s.id
                JOIN person p ON s.person_id = p.id
                WHERE c.teacher_id = ?
                ORDER BY e.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherAssignments($teacherId)
    {
        $sql = "SELECT a.*, c.name as course_name,
                       COUNT(sa.id) as total_submissions,
                       COUNT(CASE WHEN sa.grade IS NOT NULL THEN 1 END) as graded_count
                FROM assignments a
                JOIN courses c ON a.course_id = c.id
                LEFT JOIN student_assignments sa ON a.id = sa.assignment_id
                WHERE c.teacher_id = ?
                GROUP BY a.id
                ORDER BY a.due_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
