<?php

namespace App\Services;

use App\Models\Schedule;
use PDO;
use Core\Db;

class ScheduleService
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }

    public function save(Schedule $schedule)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('INSERT INTO schedules (course_id, Classe_id, date, start_time, end_time) VALUES (:course_id, :Classe_id, :date, :start_time, :end_time)');
            $stmt->execute([
                'course_id' => $schedule->getCourseId(),
                'Classe_id' => $schedule->getClasseId(),
                'date' => $schedule->getDate(),
                'start_time' => $schedule->getStartTime(),
                'end_time' => $schedule->getEndTime()
            ]);
            
            $scheduleId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            $scheduleData = [
                'id' => $scheduleId,
                'course_id' => $schedule->getCourseId(),
                'Classe_id' => $schedule->getClasseId(),
                'date' => $schedule->getDate(),
                'start_time' => $schedule->getStartTime(),
                'end_time' => $schedule->getEndTime()
            ];
            
             $schedule = new Schedule($scheduleData);

            return $schedule;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function getAll()
    {
        try {
            $sql = "SELECT 
                s.*,
                c.title as course_name,
                CONCAT(p.first_name, ' ', p.last_name) as teacher_name,
                t.id as teacher_id,
                r.number as Classe_number
                FROM schedules s
                LEFT JOIN courses c ON s.course_id = c.id
                LEFT JOIN teachers t ON c.teacher_id = t.id
                LEFT JOIN Classes r ON s.Classe_id = r.id
                LEFT JOIN person p ON t.person_id = p.id
                ORDER BY s.date, s.start_time";
                
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in ScheduleService->getAll: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM schedules WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Schedule($row);
        }
        return null;
    }

    // New methods for strategy pattern support
    public function getByClasseAndDate($ClasseId, $date)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM schedules WHERE Classe_id = :Classe_id AND date = :date');
        $stmt->execute(['Classe_id' => $ClasseId, 'date' => $date]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $schedules = [];
        foreach ($rows as $row) {
            $schedules[] = new Schedule($row);
        }
        return $schedules;
    }

    public function getByTeacherAndDate($teacherId, $date)
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM schedules s 
            JOIN courses c ON s.course_id = c.id 
            WHERE c.teacher_id = :teacher_id AND s.date = :date
        ');
        $stmt->execute(['teacher_id' => $teacherId, 'date' => $date]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $schedules = [];
        foreach ($rows as $row) {
            $schedules[] = new Schedule($row);
        }
        return $schedules;
    }

    public function getByDateRange($startDate, $endDate)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM schedules WHERE date BETWEEN :start_date AND :end_date ORDER BY date, start_time');
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $schedules = [];
        foreach ($rows as $row) {
            $schedules[] = new Schedule($row);
        }
        return $schedules;
    }

    public function getByCourseId($courseId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM schedules WHERE course_id = :course_id ORDER BY date, start_time');
        $stmt->execute(['course_id' => $courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $schedules = [];
        foreach ($rows as $row) {
            $schedules[] = new Schedule($row);
        }
        return $schedules;
    }

    public function update(Schedule $schedule)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('UPDATE schedules SET course_id = :course_id, Classe_id = :Classe_id, date = :date, start_time = :start_time, end_time = :end_time WHERE id = :id');
            $stmt->execute([
                'id' => $schedule->getId(),
                'course_id' => $schedule->getCourseId(),
                'Classe_id' => $schedule->getClasseId(),
                'date' => $schedule->getDate(),
                'start_time' => $schedule->getStartTime(),
                'end_time' => $schedule->getEndTime()
            ]);
            $this->pdo->commit();
            return $schedule;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM schedules WHERE id = :id');
        $result = $stmt->execute(['id' => $id]);
        return $result;
    }

 

}

