<?php

use App\Controllers\ParentController;
use App\Controllers\StudentController;
use App\Controllers\TeacherController;
use App\Services\EnhancedStudentService;
use App\Services\EnhancedTeacherService;
use Core\Db;

// Enhanced Student Routes
$router->get('/student/profile/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $profile = $service->getStudentProfile($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $profile]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/teachers/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $teachers = $service->getStudentTeachers($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $teachers]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/schedule/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $schedule = $service->getStudentSchedule($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $schedule]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/grades/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $grades = $service->getStudentGrades($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $grades]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/payments/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $payments = $service->getStudentPayments($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $payments]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/assignments/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $assignments = $service->getStudentAssignments($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $assignments]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/student/announcements/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedStudentService($pdo);
    try {
        $announcements = $service->getStudentAnnouncements($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $announcements]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Enhanced Teacher Routes
$router->get('/teacher/courses/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedTeacherService($pdo);
    try {
        $courses = $service->getTeacherCourses($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $courses]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/teacher/schedule/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedTeacherService($pdo);
    try {
        $schedule = $service->getTeacherSchedule($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $schedule]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/teacher/students/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedTeacherService($pdo);
    try {
        $students = $service->getTeacherStudents($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $students]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/teacher/grades/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedTeacherService($pdo);
    try {
        $grades = $service->getTeacherGrades($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $grades]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$router->get('/teacher/assignments/{id}', function($id) {
    $pdo = Db::connection();
    $service = new EnhancedTeacherService($pdo);
    try {
        $assignments = $service->getTeacherAssignments($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $assignments]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Parent Routes
$router->get('/parent/children/{id}', function($id) {
    $controller = new ParentController();
    $controller->getChildren($id);
});

$router->get('/parent/child/grades/{id}', function($id) {
    $controller = new ParentController();
    $controller->getChildGrades($id);
});

$router->get('/parent/child/schedule/{id}', function($id) {
    $controller = new ParentController();
    $controller->getChildSchedule($id);
});

$router->get('/parent/child/stats/{id}', function($id) {
    $controller = new ParentController();
    $controller->getChildStats($id);
});

$router->get('/parent/payments/{id}', function($id) {
    $controller = new ParentController();
    $controller->getPayments($id);
});

$router->get('/parent/messages/{id}', function($id) {
    $controller = new ParentController();
    $controller->getMessages($id);
});

$router->get('/parent/announcements/{id}', function($id) {
    $controller = new ParentController();
    $controller->getAnnouncements($id);
});
