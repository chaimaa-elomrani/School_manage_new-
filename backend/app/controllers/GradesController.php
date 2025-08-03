<?php

// namespace App\Controllers;
// use App\Services\GradeService;
// use Core\Db;

// class GradesController
// {
//     private $gradeService;

//     public function __construct(GradeService $gradeService = null)
//     {
//         if ($gradeService) {
//             $this->gradeService = $gradeService;
//         } else {
//             $pdo = Db::connection();
//             $this->gradeService = new GradeService($pdo);
//         }
//     }

//     public function getAll()
//     {
//         try {
//             $grades = $this->gradeService->getAll();
//             echo json_encode(['message' => 'Grades retrieved successfully', 'data' => $grades]);
//         } catch (\Exception $e) {
//             echo json_encode(['error' => $e->getMessage()]);
//         }
//     }

//     public function create()
//     {
//         $input = json_decode(file_get_contents('php://input'), true);
//         if (!$input) {
//             echo json_encode(['error' => 'Invalid JSON data']);
//             return;
//         }

//         try {
//             $result = $this->gradeService->create($input);
//             echo json_encode(['message' => 'Grade created successfully', 'data' => $result]);
//         } catch (\Exception $e) {
//             echo json_encode(['error' => $e->getMessage()]);
//         }
//     }

//     public function getById($id)
//     {
//         try {
//             $grade = $this->gradeService->getById($id);
//             echo json_encode(['message' => 'Grade found', 'data' => $grade]);
//         } catch (\Exception $e) {
//             echo json_encode(['error' => $e->getMessage()]);
//         }
//     }

//     public function update($id)
//     {
//         $input = json_decode(file_get_contents('php://input'), true);
//         if (!$input) {
//             echo json_encode(['error' => 'Invalid JSON data']);
//             return;
//         }

//         try {
//             $result = $this->gradeService->update($id, $input);
//             echo json_encode(['message' => 'Grade updated successfully', 'data' => $result]);
//         } catch (\Exception $e) {
//             echo json_encode(['error' => $e->getMessage()]);
//         }
//     }

//     public function delete($id)
//     {
//         try {
//             $this->gradeService->delete($id);
//             echo json_encode(['message' => 'Grade deleted successfully']);
//         } catch (\Exception $e) {
//             echo json_encode(['error' => $e->getMessage()]);
//         }
//     }
// }