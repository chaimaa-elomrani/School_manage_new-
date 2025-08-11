<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\StudentController; // Assuming this is for basic student CRUD
use App\Controllers\TeacherController; // Assuming this is for basic teacher CRUD
use App\Controllers\ClasseController;
use App\Controllers\CourseController;
use App\Controllers\ScheduleController;
use App\Controllers\PlanningController;
use App\Controllers\EvaluationsController;
use App\Controllers\GradesController;
use App\Controllers\BulletinController;
use App\Controllers\CourseScheduleController;
use App\Controllers\NotificationController;
use App\Controllers\PaiementEleveController; 
use App\Controllers\SalaireEnseignantController; 
use App\Controllers\FraisScolaireController; 
use App\Controllers\CommunicationController;
use App\Controllers\ParentController; 
use App\Services\EnhancedStudentService; 
use App\Services\EnhancedTeacherService;
use App\Controllers\ClassroomController; 
use Core\Db; 

// --- Authentication Routes --- tested 
$router->post('/auth/register', 'AuthController@register');
$router->post('/auth/login', 'AuthController@login');
$router->post('/auth/logout', 'AuthController@logout');


// --- Student Management Routes (Basic CRUD) ---
$router->get('/showStudent', 'StudentController@listStudents');
$router->get('/showStudent/{id}', 'StudentController@getStudentById');
$router->delete('/deleteStudent', 'StudentController@delete');

// --- Teacher Management Routes (Basic CRUD) ---
$router->get('/showTeacher', 'TeacherController@listTeachers');
$router->get('/showTeacher/{id}', 'TeacherController@getTeacherById');
$router->delete('/deleteTeacher/{id}', 'TeacherController@delete');

// --- classe Routes ---
$router->post('/createClasse', 'RoomController@create');
$router->get('/showClasses', 'RoomController@listClasses');
$router->get('/showClasse/{id}', 'RoomController@getClassById');
$router->post('/updateClasse/{id}', 'RoomController@update'); 
$router->delete('/deleteClasse/{id}', 'RoomController@delete');
$router->get('/getAvailableClasses', 'RoomController@getAvailableClasses');




// --- Course Routes ---
$router->post('/createCourse', 'CourseController@createCourse');
$router->get('/showCourses', 'CourseController@listCourses');
$router->get('/showCourse/{id}', 'CourseController@getCourseById');
$router->post('/updateCourse/{id}', 'CourseController@update'); // Consider using PUT/PATCH
$router->delete('/deleteCourse/{id}', 'CourseController@deleteCourse');




// --- Schedule Routes ---
$router->post('/schedules/plan', 'ScheduleController@planCourse');
$router->get('/schedules', 'ScheduleController@listSchedules');
$router->post('/schedules/conflicts', 'ScheduleController@getSchedulingConflicts');


// --- Evaluation Routes ---
$router->post('/evaluations', 'EvaluationController@createEvaluation');
$router->get('/evaluations', 'EvaluationController@listEvaluations');
$router->get('/evaluations/{id}', 'EvaluationController@getEvaluationById'); // e.g., /evaluations/get?id=1
$router->put('/evaluations', 'EvaluationController@updateEvaluation');
$router->delete('/evaluations', 'EvaluationController@deleteEvaluation');



// --- Note Routes ---
$router->post('/notes', 'NoteController@addNote');
$router->put('/notes', 'NoteController@updateNote');
$router->delete('/notes', 'NoteController@deleteNote');
$router->get('/notes/get', 'NoteController@getNoteById'); // e.g., /notes/get?id=1
$router->get('/notes/student', 'NoteController@getGradesByStudent'); // e.g., /notes/student?student_id=1
$router->get('/notes/evaluation', 'NoteController@getGradesByEvaluation'); // e.g., /notes/evaluation?evaluation_id=1

// --- Bulletin Routes ---
$router->get('/bulletin/generate', 'BulletinController@generateBulletin');

// --- Notifications Routes ---
$router->get('/notifications', 'NotificationController@getAll'); // Duplicate, keep one if needed

// --- Student Payment Routes ---
$router->post('/financial/payments/add', 'FinancialController@addStudentPayment');
$router->post('/financial/salaries/add', 'FinancialController@addTeacherSalary');
$router->get('/financial/payments', 'FinancialController@listStudentPayments');
$router->get('/financial/salaries', 'FinancialController@listTeacherSalaries');
$router->get('/financial/fees', 'FinancialController@listSchoolFees');



// --- Transaction Routes ---
$router->get('/transactions', 'TransactionController@listTransactions');
$router->get('/transactions/get', 'TransactionController@getTransactionById'); // e.g., /transactions/get?id=1

$router->post('/subjects', 'SubjectController@createSubject');
$router->get('/showSubjects', 'SubjectController@listSubjects');
$router->put('/subjects', 'SubjectController@updateSubject');
$router->delete('/subjects', 'SubjectController@deleteSubject');


// -- --- Logical Classroom Routes ---
$router->post('/classrooms', 'ClassroomController@createClassroom');
$router->get('/classrooms', 'ClassroomController@listClassrooms');
$router->get('/classrooms/get', 'ClassroomController@getById'); // e.g., /classrooms/get?id=1&with_students=true
$router->put('/classrooms', 'ClassroomController@update');
$router->delete('/classrooms', 'ClassroomController@delete');
$router->post('/classrooms/assign-student', 'ClassroomController@assignStudent');
$router->delete('/classrooms/unassign-student', 'ClassroomController@unassignStudent');
$router->put('/classrooms/assign-teacher', 'ClassroomController@assignTeacher'); // Using PUT for update
$router->put('/classrooms/unassign-teacher', 'ClassroomController@unassignTeacher');