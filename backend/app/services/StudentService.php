<?php

namespace App\Services;

use App\Models\Student;
use PDO;

class StudentService{

    private $pdo; 

    public function __construct(PDO $pdo){
        $this->pdo = $pdo; 
    }

    public function save(Student $student){
        $this->pdo->beginTransaction();
        
        try {
            // Insert into person table first
            $stmt = $this->pdo->prepare('INSERT INTO person (first_name, last_name, email, phone, role) VALUES (:first_name, :last_name, :email, :phone, :role)');
            $stmt->execute([
                'first_name' => $student->getFirstName(),
                'last_name' => $student->getLastName(),
                'email' => $student->getEmail(),
                'phone' => $student->getPhone(),
                'role' => $student->getRole()
            ]);
            
            $person_id = $this->pdo->lastInsertId();
            
            // Insert into students table with correct columns
            $stmt = $this->pdo->prepare('INSERT INTO students (person_id, student_number, room_id) VALUES (:person_id, :student_number, :room_id)');
            $stmt->execute([
                'person_id' => $person_id,
                'student_number' => $student->getStudentNumber(),
                'room_id' => $student->getClassId() // Use existing getClassId() method
            ]);
            
            $this->pdo->commit();
            return $student;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // explication profonde de la méthode save():
    // 1. on crée une instance de la classe PDO pour se connecter à la base de données, PDO est une classe qui permet de se connecter à la base de données
    // 2. on crée une requête préparée pour insérer les données dans la base de données, la requête préparée permet de sécuriser les données
    // 3. on exécute la requête préparée avec les données de l'étudiant
    // 4. on retourne l'étudiant
    // les getname etc se sont les getters de la classe Student il se trouve  dejat dans le model Student  , on les ai utiliser ici pour recuperer les données de l'étudiant


    public function getAll(){   
        $stmt = $this->pdo->prepare('
            SELECT s.id, s.person_id, s.student_number, s.room_id,
                   p.first_name, p.last_name, p.email, p.phone, p.role
            FROM students s 
            JOIN person p ON s.person_id = p.id
        '); 
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        $students = []; 
        
        foreach($rows as $row){ 
            $students[] = new Student($row);
        }

        return $students ; 
    }

    // explication profonde de la méthode getAll():
    // 1. on crée une instance de la classe PDO pour se connecter à la base de données, PDO est une classe qui permet de se connecter à la base de données
    // 2. on crée une requête pour sélectionner toutes les données de la table students
    // 3. on exécute la requête
    // 4. on récupère les données sous forme de tableau associatif
    // 5. on crée un tableau pour stocker les étudiants
    // 6. on parcourt le tableau des données et on crée un nouvel étudiant pour chaque ligne
    // 7. on retourne le tableau des étudiants



    public function getById($id){
        $stmt = $this->pdo->prepare('
            SELECT s.id, s.person_id, s.student_number, s.room_id,
                   p.first_name, p.last_name, p.email, p.phone, p.role
            FROM students s 
            JOIN person p ON s.person_id = p.id 
            WHERE s.id = :id
        '); 
        $stmt->execute(['id' => $id]); 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Student($row);
        }
        
        return null;
    }


    // explication profonde de la méthode getById():
    // 2. on crée une requête préparée pour sélectionner les données de l'étudiant avec l'id passé en paramètre
    // 3. on exécute la requête préparée avec l'id passé en paramètre
    // 4. on récupère les données sous forme de tableau associatif
    // 5. on crée un nouvel étudiant avec les données récupérées
    // 6. on retourne l'étudiant

    // the :id is a placeholder for the id passed in the URL

    public function update(Student $student){
        $this->pdo->beginTransaction();
        
        try {
            // Update person table
            $stmt = $this->pdo->prepare('UPDATE person SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, role = :role WHERE id = :person_id');
            $stmt->execute([
                'first_name' => $student->getFirstName(),
                'last_name' => $student->getLastName(),
                'email' => $student->getEmail(),
                'phone' => $student->getPhone(),
                'role' => $student->getRole(),
                'person_id' => $student->getPersonId()
            ]);
            
            // Update students table
            $stmt = $this->pdo->prepare('UPDATE students SET student_number = :student_number, class_id = :class_id WHERE id = :id');
            $stmt->execute([
                'student_number' => $student->getStudentNumber(),
                'class_id' => $student->getClassId(),
                'id' => $student->getId()
            ]);
            
            $this->pdo->commit();
            return $student;
        } catch (\Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }


    // explication profonde de la méthode update():
    // 2. on crée une requête préparée pour mettre à jour les données de l'étudiant avec l'id passé en paramètre
    // 3. on exécute la requête préparée avec les données de l'étudiant
    // 4. on retourne l'étudiant mis à jour
    // 5. on utilise les getters de la classe Student pour récupérer les données de l'étudiant
    // 6. on a pas utilise les setters car on ne veut pas modifier les données de l'étudiant mais seulement les données de la personne



    public function delete($id){
        // Delete from students table (person will be deleted by CASCADE)
        $stmt = $this->pdo->prepare('DELETE FROM students WHERE id = :id'); 
        $stmt->execute(['id' => $id]);
        return true;
    }
}