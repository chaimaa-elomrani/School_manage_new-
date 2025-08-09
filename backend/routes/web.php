<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\StudentController; // Assuming this is for basic student CRUD
use App\Controllers\TeacherController; // Assuming this is for basic teacher CRUD
use App\Controllers\RoomController;
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
use Core\Db; 

// --- Authentication Routes --- tested 
$router->post('/auth/register', 'AuthController@register');
$router->post('/auth/login', 'AuthController@login');
$router->post('/auth/logout', 'AuthController@logout');


// --- Student Management Routes (Basic CRUD) ---
$router->post('/createStudent', 'StudentController@create');
$router->get('/showStudent', 'StudentController@getAll');
$router->get('/showStudent/{id}', 'StudentController@getById');
$router->post('/updateStudent/{id}', 'StudentController@update'); // Consider using PUT/PATCH
$router->delete('/deleteStudent/{id}', 'StudentController@delete');

// --- Teacher Management Routes (Basic CRUD) ---
$router->post('/createTeacher', 'TeacherController@create');
$router->get('/showTeacher', 'TeacherController@getAll');
$router->get('/showTeacher/{id}', 'TeacherController@getById');
$router->post('/updateTeacher', 'TeacherController@update'); // Consider using PUT/PATCH
$router->delete('/deleteTeacher/{id}', 'TeacherController@delete');

// --- Room Routes ---
$router->post('/createRoom', 'RoomController@create');
$router->get('/showRooms', 'RoomController@getAll');
$router->get('/showRoom/{id}', 'RoomController@getById');
$router->post('/updateRoom/{id}', 'RoomController@update'); // Consider using PUT/PATCH
$router->delete('/deleteRoom/{id}', 'RoomController@delete');

// --- Course Routes ---
$router->post('/createCourse', 'CourseController@create');
$router->get('/showCourses', 'CourseController@getAll');
$router->get('/showCourse/{id}', 'CourseController@getById');
$router->post('/updateCourse/{id}', 'CourseController@update'); // Consider using PUT/PATCH
$router->delete('/deleteCourse/{id}', 'CourseController@delete');
$router->get('/showSubjects', 'SubjectController@getAll'); // Assuming SubjectController exists

// --- Schedule Routes ---
$router->post('/createSchedule', 'ScheduleController@create');
$router->get('/showSchedule/{id}', 'ScheduleController@getById');
$router->post('/updateSchedule/{id}', 'ScheduleController@update'); // Consider using PUT/PATCH
$router->delete('/deleteSchedule/{id}', 'ScheduleController@delete');
$router->post('/checkAvailability', 'ScheduleController@checkAvailability');
$router->get('/showSchedules', 'ScheduleController@index'); // Duplicate, keep one if needed

// --- Planning Routes ---
$router->post('/createPlanning', 'PlanningController@create');
$router->get('/showPlannings', 'PlanningController@getAll');
$router->get('/showPlanning/{id}', 'PlanningController@getById');
$router->post('/updatePlanning/{id}', 'PlanningController@update'); // Consider using PUT/PATCH
$router->delete('/deletePlanning/{id}', 'PlanningController@delete');
$router->get('/planning/check', 'PlanningController@checkAvailability'); // Duplicate, keep one if needed
$router->get('/planning/conflicts', 'PlanningController@getConflicts');

// --- Evaluation Routes ---
$router->post('/createEvaluation', 'EvaluationsController@create');
$router->get('/showEvaluations', 'EvaluationsController@getAll');
$router->get('/showEvaluation/{id}', 'EvaluationsController@getById');
$router->post('/updateEvaluation/{id}', 'EvaluationsController@update'); // Consider using PUT/PATCH
$router->delete('/deleteEvaluation/{id}', 'EvaluationsController@delete');

// --- Grades Routes ---
$router->post('/createGrade', 'GradesController@create');
$router->get('/showGrade/{id}', 'GradesController@getById'); // Duplicate, keep one if needed
$router->post('/updateGrade/{id}', 'GradesController@update'); // Consider using PUT/PATCH
$router->delete('/deleteGrade/{id}', 'GradesController@delete');
$router->get('/showGrades', 'GradesController@index'); // Duplicate, keep one if needed

// --- Bulletin Routes ---
$router->post('/createBulletin', 'BulletinController@create');
$router->get('/showBulletins', 'BulletinController@getAll');
$router->get('/showBulletin/{id}', 'BulletinController@getById');
$router->post('/updateBulletin/{id}', 'BulletinController@update'); // Consider using PUT/PATCH
$router->delete('/deleteBulletin/{id}', 'BulletinController@delete');
$router->post('/bulletin/generate', 'BulletinController@generate'); // Duplicate, keep one if needed

// --- Course-Schedule Strategy Routes ---
$router->post('/course-schedule/create', 'CourseScheduleController@createScheduleForCourse');
$router->get('/course-schedule/{courseId}', 'CourseScheduleController@getCourseSchedules');

// --- Notifications Routes ---
$router->get('/notifications', 'NotificationController@getAll'); // Duplicate, keep one if needed

// --- Student Payment Routes ---
$router->post('/payments/student/create', 'PaiementEleveController@create');
$router->get('/payments/student', 'PaiementEleveController@getAll');
$router->get('/payments/student/{id}', 'PaiementEleveController@getById');
$router->post('/payments/student/{id}/pay', 'PaiementEleveController@markAsPaid');
$router->post('/payment/create', 'PaiementEleveController@create'); // Duplicate, keep one if needed
$router->get('/payments', 'PaiementEleveController@getAll'); // Duplicate, keep one if needed
$router->post('/payment/process', 'PaiementEleveController@processPayment');
$router->get('/showPayments', 'PaiementEleveController@getAll'); // Duplicate, keep one if needed

// --- Teacher Salary Routes ---
$router->post('/salaries/create', 'SalaireEnseignantController@create');
$router->get('/salaries', 'SalaireEnseignantController@getAll');
$router->get('/salaries/{id}', 'SalaireEnseignantController@getById');
$router->post('/salaries/{id}/pay', 'SalaireEnseignantController@markAsPaid');

// --- School Fees Routes ---
$router->post('/school-fees/create', 'FraisScolaireController@create');
$router->get('/school-fees', 'FraisScolaireController@getAll');
$router->get('/school-fees/{id}', 'FraisScolaireController@getById');

// --- Communication Routes ---
$router->post('/communication/email', 'CommunicationController@sendEmailNotification');
$router->post('/communication/sms', 'CommunicationController@sendSMSNotification');
$router->post('/communication/message', 'CommunicationController@sendInternalMessage');
$router->post('/communication/broadcast', 'CommunicationController@broadcastNotification');

// --- ENHANCED STUDENT DASHBOARD ROUTES (using EnhancedStudentService) ---
$router->get('/student/profile/{id}', 'StudentController@getEnhancedProfile');
$router->get('/student/teachers/{id}', 'StudentController@getEnhancedTeachers');
$router->get('/student/schedule/{id}', 'StudentController@getEnhancedSchedule');
$router->get('/student/grades/{id}', 'StudentController@getEnhancedGrades');
$router->get('/student/payments/{id}', 'StudentController@getEnhancedPayments');
$router->get('/student/assignments/{id}', 'StudentController@getEnhancedAssignments');
$router->get('/student/announcements/{id}', 'StudentController@getEnhancedAnnouncements');

// --- ENHANCED TEACHER DASHBOARD ROUTES (using EnhancedTeacherService) ---
$router->get('/teacher/courses/{id}', 'TeacherController@getEnhancedCourses');
$router->get('/teacher/schedule/{id}', 'TeacherController@getEnhancedSchedule');
$router->get('/teacher/students/{id}', 'TeacherController@getEnhancedStudents');
$router->get('/teacher/grades/{id}', 'TeacherController@getEnhancedGrades');
$router->get('/teacher/assignments/{id}', 'TeacherController@getEnhancedAssignments');

// --- PARENT DASHBOARD ROUTES (using UpdatedParentController) ---
$router->get('/parent/profile/{id}', 'ParentController@getProfile');
$router->get('/parent/children/{id}', 'ParentController@getChildren');
$router->get('/parent/child/grades/{id}', 'ParentController@getChildGrades');
$router->get('/parent/child/schedule/{id}', 'ParentController@getChildSchedule');
$router->get('/parent/child/stats/{id}', 'ParentController@getChildStats');
$router->get('/parent/child/teachers/{id}', 'ParentController@getChildTeachers');
$router->get('/parent/payments/{id}', 'ParentController@getPayments');
$router->get('/parent/messages/{id}', 'ParentController@getMessages');
$router->get('/parent/announcements/{id}', 'ParentController@getAnnouncements');
$router->post('/parent/send-message', 'ParentController@sendMessage');

// --- Other specific routes (review and remove if redundant) ---
$router->get('/studentData/{id}' , 'StudentController@getStudentData'); // Likely redundant with /student/profile/{id}
$router->get('/getStudentData/{id}', 'StudentController@getStudentData'); // Likely redundant
$router->get('/getClassTeachers/{classId}', 'StudentController@getClassTeachers'); // Keep if needed for other parts
$router->get('/getClassmates/{classId}', 'StudentController@getClassmates'); // Keep if needed for other parts
$router->get('/getStudentSchedule/{studentId}', 'ScheduleController@getStudentSchedule'); // Redundant with /student/schedule/{id}
$router->get('/getStudentEvaluations/{studentId}', 'EvaluationController@getStudentEvaluations'); // Redundant with /student/grades/{id}
$router->get('/getStudentGrades/{studentId}', 'GradesController@getStudentGrades'); // Redundant with /student/grades/{id}
// ... and so on for other duplicates.
