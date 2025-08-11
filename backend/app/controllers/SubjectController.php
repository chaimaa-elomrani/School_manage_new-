<?php

namespace App\Controllers;

use App\Services\SubjectService;
use App\Models\Subject;

class SubjectController
{
    private SubjectService $subjectService;

    public function __construct()
    {
        $this->subjectService = new SubjectService();
    }

    /**
     * Handles creating a new subject.
     * Method: POST
     * Body: { "name": "New Subject Name", "description": "Description of the new subject" }
     */
    public function createSubject(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['name'])) {
            $this->sendJsonResponse(['error' => 'Subject name is required.'], 400);
            return;
        }

        try {
            $subject = new Subject($input);
            $newSubject = $this->subjectService->create($subject);
            $this->sendJsonResponse(['message' => 'Subject created successfully.', 'subject' => $newSubject->toArray()], 201);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to create subject: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lists all subjects.
     * Method: GET
     * URL: /subjects
     */
    public function listSubjects(): void
    {
        try {
            $subjects = $this->subjectService->getAllSubjects();
            $this->sendJsonResponse(['subjects' => array_map(fn($s) => $s->toArray(), $subjects)]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve subjects: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gets a single subject by ID.
     * Method: GET
     * URL: /subjects/get?id=1
     */
    public function getSubjectById(array $params): void
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            $this->sendJsonResponse(['error' => 'Subject ID is required.'], 400);
            return;
        }

        try {
            $subject = $this->subjectService->getSubjectById((int)$id);
            if ($subject) {
                $this->sendJsonResponse(['subject' => $subject->toArray()]);
            } else {
                $this->sendJsonResponse(['error' => 'Subject not found.'], 404);
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to retrieve subject: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing subject.
     * Method: PUT
     * Body: { "id": 1, "name": "Updated Subject Name", "description": "New description" }
     */
    public function updateSubject(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id'])) {
            $this->sendJsonResponse(['error' => 'Subject ID is required for update.'], 400);
            return;
        }

        try {
            $existingSubject = $this->subjectService->getSubjectById((int)$input['id']);
            if (!$existingSubject) {
                $this->sendJsonResponse(['error' => 'Subject not found.'], 404);
                return;
            }

            // Update properties from input, keeping existing if not provided
            $data = $existingSubject->toArray();
            $data['name'] = $input['name'] ?? $data['name'];
            $data['description'] = $input['description'] ?? $data['description'];

            $updatedSubject = new Subject($data);
            $this->subjectService->update($updatedSubject);
            $this->sendJsonResponse(['message' => 'Subject updated successfully.', 'subject' => $updatedSubject->toArray()]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to update subject: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a subject by ID.
     * Method: DELETE
     * Body: { "id": 1 }
     */
    public function deleteSubject(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->sendJsonResponse(['error' => 'Subject ID is required for deletion.'], 400);
            return;
        }

        try {
            if ($this->subjectService->delete((int)$id)) {
                $this->sendJsonResponse(['message' => 'Subject deleted successfully.']);
            } else {
                $this->sendJsonResponse(['error' => 'Subject not found or could not be deleted.'], 404);
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Failed to delete subject: ' . $e->getMessage()], 500);
        }
    }

    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
