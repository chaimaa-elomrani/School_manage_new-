<?php

namespace App\Services;

use App\Models\Teacher;
use PDO;
use Core\Db;

class TeacherService{

    private $pdo; 

    public function __construct(){
         $this->pdo = Db::connection();
    }

    public function listTeachers(){
        $stmt = $this->pdo->prepare('SELECT t.person_id , t.employee_number , p.first_name , p.last_name , p.email , p.phone, ct.classroom_id , c.name, c.academic_year
         FROM teachers t JOIN classroom_teachers ct ON t.id = ct.teacher_id 
         JOIN classrooms c ON ct.classroom_id = c.id 
         JOIN person p ON t.person_id = p.id'); 
        $stmt->execute(); 
         foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $teachers[] = new Teacher($row);
        }
        return $teachers;
    }

    public function getTeacherById($id){
        $stmt = $this->pdo->prepare('SELECT * FROM teachers WHERE id = :id'); 
        $stmt->execute(['id' => $id]); 
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC); 
        return $teacher; 
    }


    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM teachers WHERE id = :id'); 
        $stmt->execute(['id' => $id]);
        return true;
    }
}