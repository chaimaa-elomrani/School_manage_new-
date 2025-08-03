<?php

use Core\Router; 

// Authentication routes
$router->post('/auth/register', 'AuthController@register');
$router->post('/auth/login', 'AuthController@login');
$router->post('/auth/logout', 'AuthController@logout');
$router->get('/auth/me', 'AuthController@me');

// student routes  tested
$router->post('/createStudent', 'StudentController@create');
$router->get('/showStudent', 'StudentController@getAll');
$router->get('/showStudent/{id}', 'StudentController@getById');
$router->post('/updateStudent/{id}', 'StudentController@update');
$router->delete('/deleteStudent/{id}', 'StudentController@delete');

// teacher routes tested
$router->post('/createTeacher', 'TeacherController@create');
$router->get('/showTeacher', 'TeacherController@getAll');
$router->get('/showTeacher/{id}', 'TeacherController@getById');
$router->post('/updateTeacher', 'TeacherController@update');
$router->delete('/deleteTeacher/{id}', 'TeacherController@delete');

// room routes tested successfuly 
$router->post('/createRoom', 'RoomController@create');
$router->get('/showRooms', 'RoomController@getAll');
$router->get('/showRoom/{id}', 'RoomController@getById');
$router->post('/updateRoom/{id}', 'RoomController@update');
$router->delete('/deleteRoom/{id}', 'RoomController@delete');


// courses routes tested
$router->post('/createCourse', 'CourseController@create');
$router->get('/showCourses', 'CourseController@getAll');
$router->get('/showCourse/{id}', 'CourseController@getById');
$router->post('/updateCourse/{id}', 'CourseController@update');
$router->delete('/deleteCourse/{id}', 'CourseController@delete');


// schedule routes tested 
$router->post('/createSchedule', 'ScheduleController@create');
$router->get('/showSchedule/{id}', 'ScheduleController@getById');
$router->post('/updateSchedule/{id}', 'ScheduleController@update');
$router->delete('/deleteSchedule/{id}', 'ScheduleController@delete');
$router->post('/checkAvailability', 'ScheduleController@checkAvailability');


// plannig routes tested
$router->post('/createPlanning', 'PlanningController@create');
$router->get('/showPlannings', 'PlanningController@getAll');
$router->get('/showPlanning/{id}', 'PlanningController@getById');
$router->post('/updatePlanning/{id}', 'PlanningController@update');
$router->delete('/deletePlanning/{id}', 'PlanningController@delete');


// evaluations route tested 
$router->post('/createEvaluation', 'EvaluationsController@create');
$router->get('/showEvaluations', 'EvaluationsController@getAll');
$router->get('/showEvaluation/{id}', 'EvaluationsController@getById');
$router->post('/updateEvaluation/{id}', 'EvaluationsController@update');
$router->delete('/deleteEvaluation/{id}', 'EvaluationsController@delete');


// routes tested
$router->post('/createGrade', 'GradesController@create');
$router->get('/showGrade/{id}', 'GradesController@getById');
$router->get('/showGrade/{id}', 'GradesController@getById');
$router->post('/updateGrade/{id}', 'GradesController@update');
$router->delete('/deleteGrade/{id}', 'GradesController@delete');

// routes tested 
$router->post('/createBulletin', 'BulletinController@create');
$router->get('/showBulletins', 'BulletinController@getAll');
$router->get('/showBulletin/{id}', 'BulletinController@getById');
$router->post('/updateBulletin/{id}', 'BulletinController@update');
$router->delete('/deleteBulletin/{id}', 'BulletinController@delete');
$router->post('/bulletin/generate', 'BulletinController@generate');

// Course-Schedule Strategy routes
$router->post('/course-schedule/create', 'CourseScheduleController@createScheduleForCourse');
$router->get('/course-schedule/{courseId}', 'CourseScheduleController@getCourseSchedules');

$router->get('/notifications', 'NotificationController@getAll');

// Student Payment Routes
$router->post('/payments/student/create', 'PaiementEleveController@create');
$router->get('/payments/student', 'PaiementEleveController@getAll');
$router->get('/payments/student/{id}', 'PaiementEleveController@getById');
$router->post('/payments/student/{id}/pay', 'PaiementEleveController@markAsPaid');

// Teacher Salary Routes
$router->post('/salaries/create', 'SalaireEnseignantController@create');
$router->get('/salaries', 'SalaireEnseignantController@getAll');
$router->get('/salaries/{id}', 'SalaireEnseignantController@getById');
$router->post('/salaries/{id}/pay', 'SalaireEnseignantController@markAsPaid');

// School Fees Routes
$router->post('/school-fees/create', 'FraisScolaireController@create');
$router->get('/school-fees', 'FraisScolaireController@getAll');
$router->get('/school-fees/{id}', 'FraisScolaireController@getById');

// Communication routes
$router->post('/communication/email', 'CommunicationController@sendEmailNotification');
$router->post('/communication/sms', 'CommunicationController@sendSMSNotification');
$router->post('/communication/message', 'CommunicationController@sendInternalMessage');
$router->post('/communication/broadcast', 'CommunicationController@broadcastNotification');

// Bulletin generation route
$router->post('/bulletin/generate', 'BulletinController@generate');

// Notifications route
$router->get('/notifications', 'NotificationController@getAll');

// Financial routes
$router->post('/payment/create', 'PaiementEleveController@create');
$router->get('/payments', 'PaiementEleveController@getAll');
$router->post('/payment/process', 'PaiementEleveController@processPayment');

// Planning routes
$router->post('/planning/create', 'PlanningController@create');
$router->get('/planning/check', 'PlanningController@checkAvailability');
$router->get('/planning/conflicts', 'PlanningController@getConflicts');

$router->get('/showPayments', 'PaymentController@getAll');

// Student Dashboard routes

$router->get('/showSchedules', 'ScheduleController@index');
$router->get('/showGrades', 'GradeController@index');
$router->get('/showCourses', 'CourseController@index');
$router->get('/showEnrollments', 'EnrollmentController@index');

// Debug route for student data
$router->get('/student/{id}/data', 'StudentController@getStudentData');

// Student routes
$router->get('/showStudent', 'StudentController@getAll');
$router->post('/createStudent', 'StudentController@create');

// Teacher routes
$router->get('/showTeacher', 'TeacherController@getAll');
$router->post('/createTeacher', 'TeacherController@create');

// Course routes
$router->get('/showCourses', 'CourseController@getAll');
$router->post('/createCourse', 'CourseController@create');

// Communication routes
$router->post('/communication/email', 'CommunicationController@sendEmailNotification');
$router->post('/communication/sms', 'CommunicationController@sendSMSNotification');
$router->post('/communication/message', 'CommunicationController@sendInternalMessage');
$router->post('/communication/broadcast', 'CommunicationController@broadcastNotification');

// Subject routes
$router->get('/showSubjects', 'SubjectController@getAll');
