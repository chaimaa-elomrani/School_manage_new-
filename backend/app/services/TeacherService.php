<?php

namespace App\Services;

use App\Models\Teacher;
use PDO;

class TeacherService{

    private $pdo; 

    public function __construct(PDO $pdo){
        $this->pdo = $pdo; 
    }

    public function save(Teacher $teacher){
        $this->pdo->beginTransaction();
        
        try {
            // Insert into person table first
            $stmt = $this->pdo->prepare('INSERT INTO person (first_name, last_name, email, phone, role) VALUES (:first_name, :last_name, :email, :phone, :role)');
            $stmt->execute([
                'first_name' => $teacher->getFirstName(),
                'last_name' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
                'phone' => $teacher->getPhone(),
                'role' => $teacher->getRole()
            ]);
            
            $person_id = $this->pdo->lastInsertId();
            
            // Insert into teachers table
            $stmt = $this->pdo->prepare('INSERT INTO teachers (person_id, employee_number, specialty) VALUES (:person_id, :employee_number, :specialty)');
            $stmt->execute([
                'person_id' => $person_id,
                'employee_number' => $teacher->getEmployeeNumber(),
                'specialty' => $teacher->getSpecialty()
            ]);
            
            $this->pdo->commit();
            return $teacher;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }


    public function getAll(){
        $stmt = $this->pdo->prepare('
            SELECT t.id, t.person_id, t.employee_number, t.specialty,
                   p.first_name, p.last_name, p.email, p.phone, p.role
            FROM teachers t 
            JOIN person p ON t.person_id = p.id
        '); 
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        $teachers = [];
        
        foreach($rows as $row){
            $teachers[] = new Teacher($row);
        }
        
        return $teachers;
    }


    public function getById($id){
        $stmt = $this->pdo->prepare('
            SELECT t.id, t.person_id, t.employee_number, t.specialty,
                   p.first_name, p.last_name, p.email, p.phone, p.role
            FROM teachers t 
            JOIN person p ON t.person_id = p.id 
            WHERE t.id = :id
        '); 
        $stmt->execute(['id' => $id]); 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Teacher($row);
        }
        
        return null;
    }


    public function update(Teacher $teacher){
        $this->pdo->beginTransaction();
        
        try {
            // Update person table
            $stmt = $this->pdo->prepare('UPDATE person SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, role = :role WHERE id = :person_id');
            $stmt->execute([
                'first_name' => $teacher->getFirstName(),
                'last_name' => $teacher->getLastName(),
                'email' => $teacher->getEmail(),
                'phone' => $teacher->getPhone(),
                'role' => $teacher->getRole(),
                'person_id' => $teacher->getPersonId()
            ]);
            
            // Update teachers table
            $stmt = $this->pdo->prepare('UPDATE teachers SET employee_number = :employee_number, specialty = :specialty WHERE id = :id');
            $stmt->execute([
                'employee_number' => $teacher->getEmployeeNumber(),
                'specialty' => $teacher->getSpecialty(),
                'id' => $teacher->getId()
            ]);
            
            $this->pdo->commit();
            return $teacher;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }


    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM teachers WHERE id = :id'); 
        $stmt->execute(['id' => $id]);
        return true;
    }
}