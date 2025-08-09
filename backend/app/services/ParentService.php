<?php

namespace App\Services;

use PDO;

class ParentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listParents(){
        $stmt = $this->pdo->prepare('SELECT * FROM parents'); 
        $stmt->execute(); 
        $parents = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        return $parents;
    }

    public function getParentById($id){
        $stmt = $this->pdo->prepare('SELECT * FROM parents WHERE id = :id'); 
        $stmt->execute(['id' => $id]); 
        $parent = $stmt->fetch(PDO::FETCH_ASSOC); 
        return $parent;
    }
    
    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM parents WHERE id = :id'); 
        $stmt->execute(['id' => $id]);
        return true;
    }

  }
