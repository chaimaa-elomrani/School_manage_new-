<?php

namespace App\Services;

use PDO;

class ParentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getChildren($parentId)
    {
        $sql = "SELECT s.*, p.first_name, p.last_name, p.email, p.phone 
                FROM students s 
                JOIN person p ON s.person_id = p.id 
                WHERE s.id IN (
                    SELECT student_id FROM parent_student_relations 
                    WHERE parent_id = ?
                )";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildGrades($childId)
    {
        $sql = "SELECT g.*, s.name as subject_name, c.name as course_name,
                       e.type as evaluation_type, e.date
                FROM grades g
                JOIN evaluations e ON g.evaluation_id = e.id
                JOIN courses c ON e.course_id = c.id
                JOIN subjects s ON c.subject_id = s.id
                WHERE g.student_id = ?
                ORDER BY e.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$childId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildSchedule($childId)
    {
        $sql = "SELECT sc.*, c.name as course_name, r.number as room_number,
                       CONCAT(p.first_name, ' ', p.last_name) as teacher_name
                FROM schedules sc
                JOIN courses c ON sc.course_id = c.id
                JOIN rooms r ON sc.room_id = r.id
                JOIN teachers t ON c.teacher_id = t.id
                JOIN person p ON t.person_id = tp.id
                WHERE sc.course_id IN (
                    SELECT course_id FROM enrollments WHERE student_id = ?
                )
                ORDER BY sc.day_of_week, sc.start_time";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$childId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildStats($childId)
    {
        // Get average grade
        $gradesSql = "SELECT AVG(g.note) as average_grade
                      FROM grades g
                      JOIN evaluations e ON g.evaluation_id = e.id
                      WHERE g.student_id = ?";
        
        $stmt = $this->pdo->prepare($gradesSql);
        $stmt->execute([$childId]);
        $gradeResult = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get attendance rate (mock data for now)
        $attendanceRate = 95; // This would come from attendance table

        // Get assignments due
        $assignmentsSql = "SELECT COUNT(*) as assignments_due
                          FROM assignments a
                          JOIN courses c ON a.course_id = c.id
                          JOIN enrollments e ON c.id = e.course_id
                          WHERE e.student_id = ? AND a.due_date > NOW()
                          AND a.id NOT IN (
                              SELECT assignment_id FROM student_assignments 
                              WHERE student_id = ? AND submitted = 1
                          )";
        
        $stmt = $this->pdo->prepare($assignmentsSql);
        $stmt->execute([$childId, $childId]);
        $assignmentResult = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get upcoming exams
        $examsSql = "SELECT COUNT(*) as upcoming_exams
                     FROM evaluations e
                     JOIN courses c ON e.course_id = c.id
                     JOIN enrollments en ON c.id = en.course_id
                     WHERE en.student_id = ? AND e.date > NOW() AND e.type = 'exam'";
        
        $stmt = $this->pdo->prepare($examsSql);
        $stmt->execute([$childId]);
        $examResult = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'average_grade' => round($gradeResult['average_grade'] ?? 0, 2),
            'attendance_rate' => $attendanceRate,
            'assignments_due' => $assignmentResult['assignments_due'] ?? 0,
            'upcoming_exams' => $examResult['upcoming_exams'] ?? 0
        ];
    }

    public function getPayments($parentId)
    {
        $sql = "SELECT p.*, CONCAT(sp.first_name, ' ', sp.last_name) as child_name
                FROM payments p
                JOIN students s ON p.student_id = s.id
                JOIN person sp ON s.person_id = sp.id
                WHERE s.id IN (
                    SELECT student_id FROM parent_student_relations 
                    WHERE parent_id = ?
                )
                ORDER BY p.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessages($parentId)
    {
        $sql = "SELECT m.*, CONCAT(tp.first_name, ' ', tp.last_name) as sender_name
                FROM messages m
                JOIN person tp ON m.sender_id = tp.id
                WHERE m.recipient_id = ? OR m.recipient_type = 'parent'
                ORDER BY m.created_at DESC
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnnouncements($parentId)
    {
        $sql = "SELECT n.*, nr.read_at
                FROM notifications n
                LEFT JOIN notification_recipients nr ON n.id = nr.notification_id 
                    AND nr.recipient_id = ?
                WHERE n.type = 'announcement' 
                    AND (n.target_audience = 'all' OR n.target_audience = 'parents')
                ORDER BY n.created_at DESC
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // public function getChildTeacher($childId){

    //     try {
    //         $sql = "SELECT "
    //     }
    // }
}
