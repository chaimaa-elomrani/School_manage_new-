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

    public function listStudents()
    {

        $sql = "SELECT s.person_id , p.first_name , p.last_name, p.email , p.phone FROM students s
                JOIN person p ON s.person_id = p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $students;
    }

    public function getStudentById($id)
    {

        $sql = "SELECT s.person_id , p.first_name , p.last_name, p.email , p.phone FROM students s
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