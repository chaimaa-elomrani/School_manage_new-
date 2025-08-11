<?php

namespace App\Services;

use App\Models\Student;
use PDO;
use Core\Db;

class StudentService
{

    private $pdo;

    public function __construct()
    {
         $this->pdo = Db::connection();
    }

 public function listStudents(): array
    {
        $stmt = $this->pdo->prepare('SELECT s.person_id, s.classroom_id , p.first_name , p.last_name, p.email , p.phone , c.name , c.academic_year, ct.teacher_id , t.person_id  FROM students s
                JOIN classrooms c ON s.classroom_id = c.id
                JOIN classroom_teachers ct ON c.id = ct.classroom_id
                JOIN teachers t ON ct.teacher_id = t.id
                JOIN person p ON s.person_id = p.id');
        $stmt->execute();
        $students = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            // Assuming your Student model can be instantiated with an associative array
            $students[] = new Student($row);
        }
        return $students;
    }

    public function getStudentById($id)
    {

        $sql = "SELECT s.person_id, s.classroom_id , p.first_name , p.last_name, p.email , p.phone , c.name , c.academic_year, ct.teacher_id , t.person_id  FROM students s
                JOIN classrooms c ON s.classroom_id = c.id
                JOIN classroom_teachers ct ON c.id = ct.classroom_id
                JOIN teachers t ON ct.teacher_id = t.id
                JOIN person p ON s.person_id = p.id
                WHERE s.person_id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        return $student;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM students WHERE person_id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }

}