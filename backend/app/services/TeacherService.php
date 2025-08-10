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
        $stmt = $this->pdo->prepare('SELECT * FROM teachers'); 
        $stmt->execute(); 
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC); 
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