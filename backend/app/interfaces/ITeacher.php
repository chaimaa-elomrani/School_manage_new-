<?php

namespace App\Interfaces;

interface ITeacher extends IPerson{

    public function getSubject(); 

    public function setSubject($subject);

    public function getSalary(); 

    public function setSalary($salaire);

    public function getAbsence();

    public function setAbsence($absence);   
    
}