<?php

namespace App\Interfaces;
use PDO; 

interface IPerson {
 
    public function  getId();

    public function getFirstName();

    public function getLastName(); 

    public function getEmail(); 
    
    public function getPhone();
    
    public function save(PDO $pdo);


}
