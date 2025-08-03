<?php
namespace App\Interfaces;

interface IStudent extends IPerson{

    public function getClass();

    public function setClass($class);

    public function getStudentNumber();

    public function setStudentNumber($studentNumber);

    public function getAbsence();
    
    public function setAbsence($absence); 

}