<?php

namespace App\Interfaces;

interface IPerson {
 
    public function  getId();

    public function getName(); 

    public function setName($name);


    public function getEmail(); 

    public function setEmail($email); 

    public function getRole(); 
    
    public function toArray();


}
