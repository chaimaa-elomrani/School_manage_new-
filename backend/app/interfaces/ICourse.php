<?php
namespace App\Interfaces; 

interface ICourse{

    // getters
    public function getId();
    public function getName();
    public function getTeacherId(); 
    public function getSubjectId();
    public function getRoomId();
    public function getDuration();
    public function getLevel();
    public function getCourseStartDate();
    public function getCourseEndDate();

   
}