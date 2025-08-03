<?php
namespace App\Interfaces;

interface ISchedule{

    public function  getId();
    public function getCourseId();
    public function getRoomId(); 
    public function getTeacherId();
    public function getDate();
    public function getStartTime();
    public function getEndTime();

}