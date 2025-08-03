<?php

namespace App\Factories;

use App\Models\Student;
use App\Models\Teacher;
use App\Interfaces\IPerson;

class PersonFactory{
    public static function createPerson(string $role, array $data): ?IPerson
    {
        if ($role === 'student') {
            $newStudent = new Student($data);
            return $newStudent;
        }else if ($role === 'teacher'){
            $newTeacher = new Teacher($data);
            return $newTeacher;
        }else {
            return null ; 
        }
    }
}
