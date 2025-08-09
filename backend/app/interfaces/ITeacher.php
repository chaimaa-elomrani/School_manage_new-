<?php

namespace App\Interfaces;

interface ITeacher extends IPerson{

    public function getSpeciality(); 
    
    public function getEmployeeNumber();
}