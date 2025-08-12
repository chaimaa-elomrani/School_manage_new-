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

   public function create(array $data)
{
    $sql = "INSERT INTO courses (title, description, subject_id, teacher_id, class_id, duration, start_date, end_date)
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

    return $this->getCourseById($this->pdo->lastInsertId());
}


    public function listCourses()
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.title, c.description, c.subject_id, c.teacher_id, c.class_id, c.duration, c.start_date, c.end_date,
                    t.specialty, t.person_id, 
                    p.first_name, p.last_name, 
                    s.name as subject_name, 
                    cl.number as class_number
             FROM courses c 
             JOIN subjects s ON c.subject_id = s.id
             JOIN teachers t ON c.teacher_id = t.id
             JOIN classes cl ON c.class_id = cl.id
             JOIN person p ON t.person_id = p.id"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $courses = [];
        foreach ($rows as $row) {
            $courseData = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'subject_id' => $row['subject_id'],
                'teacher_id' => $row['teacher_id'],
                'class_id' => $row['class_id'],
                'duration' => $row['duration'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'teacher_name' => $row['first_name'] . ' ' . $row['last_name'],
                'subject_name' => $row['subject_name'],
                'class_number' => $row['class_number'],
                'specialty' => $row['specialty']
            ];
            $courses[] = new Course($courseData);
        }
        return $courses;
    }

    public function getCourseById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.title, c.description, c.subject_id, c.teacher_id, c.class_id, c.duration, c.start_date, c.end_date,
                    t.specialty, t.person_id, 
                    p.first_name, p.last_name, 
                    s.name as subject_name, 
                    cl.number as class_number
             FROM courses c 
             JOIN subjects s ON c.subject_id = s.id
             JOIN teachers t ON c.teacher_id = t.id
             JOIN classes cl ON c.class_id = cl.id
             JOIN person p ON t.person_id = p.id
             WHERE c.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        $courseData = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'subject_id' => $row['subject_id'],
            'teacher_id' => $row['teacher_id'],
            'class_id' => $row['class_id'],
            'duration' => $row['duration'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'teacher_name' => $row['first_name'] . ' ' . $row['last_name'],
            'subject_name' => $row['subject_name'],
            'class_number' => $row['class_number'],
            'specialty' => $row['specialty']
        ];
        
        return new Course($courseData);
    }

    public function update(array $data)
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE courses SET 
                    title = :title, 
                    description = :description, 
                    subject_id = :subject_id, 
                    teacher_id = :teacher_id, 
                    class_id = :class_id, 
                    duration = :duration, 
                    start_date = :start_date, 
                    end_date = :end_date 
                    WHERE id = :id";
            
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
        return $stmt->rowCount() > 0;
    }
}
