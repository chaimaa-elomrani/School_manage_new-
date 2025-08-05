<?php

namespace App\Services;

use PDO;

class EnhancedStudentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getStudentProfile($studentId)
    {
        $sql = "SELECT s.*, p.first_name, p.last_name, p.email, p.phone,
                       COUNT(DISTINCT cs.student_id) as classmates_count
                FROM students s
                JOIN person p ON s.person_id = p.id
                LEFT JOIN students cs ON s.class_id = cs.class_id AND cs.id != s.id
                WHERE s.id = ?
                GROUP BY s.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentTeachers($studentId)
    {
        $sql = "SELECT DISTINCT t.*, p.first_name as nom, p.last_name as prenom, 
                       s.name as specialite
                FROM teachers t
                JOIN person p ON t.person_id = p.id
                JOIN subjects s ON t.specialty = s.name
                JOIN courses c ON t.id = c.teacher_id
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.student_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentSchedule($studentId)
    {
        $sql = "SELECT sc.*, c.name as course_name, r.number as room_number,
                       CONCAT(tp.first_name, ' ', tp.last_name) as teacher_name
                FROM schedules sc
                JOIN courses c ON sc.course_id = c.id
                JOIN rooms r ON sc.room_id = r.id
                JOIN teachers t ON c.teacher_id = t.id
                JOIN person tp ON t.person_id = tp.id
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.student_id = ?
                ORDER BY sc.day_of_week, sc.start_time";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentGrades($studentId)
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
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentPayments($studentId)
    {
        $sql = "SELECT * FROM payments 
                WHERE student_id = ? 
                ORDER BY date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentAssignments($studentId)
    {
        $sql = "SELECT a.*, c.name as course_name,
                       sa.submitted, sa.submitted_at, sa.grade
                FROM assignments a
                JOIN courses c ON a.course_id = c.id
                JOIN enrollments e ON c.id = e.course_id
                LEFT JOIN student_assignments sa ON a.id = sa.assignment_id 
                    AND sa.student_id = ?
                WHERE e.student_id = ?
                ORDER BY a.due_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId, $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentAnnouncements($studentId)
    {
        $sql = "SELECT n.*, nr.read_at
                FROM notifications n
                LEFT JOIN notification_recipients nr ON n.id = nr.notification_id 
                    AND nr.recipient_id = ?
                WHERE n.type = 'announcement' 
                    AND (n.target_audience = 'all' OR n.target_audience = 'students')
                ORDER BY n.created_at DESC
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
