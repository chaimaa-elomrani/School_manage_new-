<?php
namespace App\Interfaces;

interface IStudent extends IPerson{

    public function getRoom();

    public function setRoom($class);

    public function getStudentNumber();

    public function setStudentNumber($studentNumber);

    public function getAbsence();
    
    public function setAbsence($absence); 

}