<?php

namespace App\Services;

use App\Models\Course;
use PDO;
use Core\Db;

class CourseService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::connection();
    }


    public function createCourse(array $data)
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO courses (title, description, subject_id, teacher_id, class_id, duration, start_date, end_date ) 
        VALUES (:title, :description, :subject_id, :teacher_id, :class_id, :duration, :start_date, :end_date)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
                'class_id' => $data['class_id'],
                'duration' => $data['duration'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);
            $courseId = $this->pdo->lastInsertId();
            $course = $this->getCourseById($courseId);
            $this->pdo->commit();
            return $course;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }


    public function listCourses()
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.title , c.description, c.subject_id, c.teacher_id, c.class_id, c.duration, c.start_date, c.end_date , t.person_id, p.first_name, p.last_name, s.name, cl.number 
            FROM courses c JOIN subjects s ON c.subject_id = s.id
            JOIN teachers t ON c.teacher_id = t.id
            JOIN classes cl ON c.class_id = cl.id
            JOIN person p ON t.person_id = p.id"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $courses = [];
        foreach ($rows as $row) {
            $courses[] = new Course($row);
        }
        return $courses;
    }


    public function getCourseById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $Course = $stmt->fetch(PDO::FETCH_ASSOC);
        return $Course;

      
    }


    public function update(array $data)
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE courses SET title = :title, description = :description, subject_id = :subject_id, teacher_id = :teacher_id, class_id = :class_id, duration = :duration, start_date = :start_date, end_date = :end_date WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $data['id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
                'class_id' => $data['class_id'],
                'duration' => $data['duration'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);
            $this->pdo->commit();
            return $this->getCourseById($data['id']);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }

}