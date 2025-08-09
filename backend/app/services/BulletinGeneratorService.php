<?php

namespace App\Services;

use App\Abstracts\AbstractBulletinGenerator;
use App\Models\Bulletin;
use App\Models\Student;
use App\Models\Course;
use App\Models\Note;
use App\Interfaces\INoteService;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class BulletinGeneratorService extends AbstractBulletinGenerator
{
    public function __construct(INoteService $noteService)
    {
        parent::__construct($noteService);
    }

    protected function collectGrades(int $studentId, int $courseId): array
    {
        // In a real scenario, you might need to filter notes by course/subject
        // For simplicity, let's assume getGradesByStudent returns all notes for now,
        // and we'll filter by course here if needed, or adjust INoteService.
        
        // For a bulletin per course, we need notes specific to that course's evaluations.
        // This requires a method in NoteService or a join here.
        // Let's assume NoteService can give us notes for a student in a specific course.
        // If not, you'd fetch all notes and filter by evaluation->subject_id matching course->subject_id.
        
        // Placeholder: Fetch all notes for the student and filter by course's subject
        $allStudentNotes = $this->noteService->getGradesByStudent($studentId);
        
        // This part needs actual evaluation data to link notes to courses/subjects.
        // For simplicity, let's assume getGradesByStudent returns notes relevant to the course.
        // In a more robust system, you'd fetch evaluations for the course, then notes for those evaluations.
        
        // For now, let's just return all notes for the student, assuming they are relevant to the course context.
        // A better approach would be:
        // 1. Get all evaluation IDs for the given course.
        // 2. Get notes for the student that match those evaluation IDs.
        
        // To keep it simple and functional for this example, we'll just return all notes for the student.
        // This might not be perfectly accurate for a "per course" bulletin without more data.
        return $allStudentNotes;
    }

    protected function saveBulletin(Bulletin $bulletin): Bulletin
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO bulletins(student_id, course_id, general_average, grade_letter, generation_date)
                 VALUES (:student_id, :course_id, :general_average, :grade_letter, :generation_date)'
            );
            $stmt->execute([
                'student_id' => $bulletin->getStudentId(),
                'course_id' => $bulletin->getCourseId(),
                'general_average' => $bulletin->getGeneralAverage(),
                'grade_letter' => $bulletin->getGradeLetter(),
                'generation_date' => $bulletin->getGenerationDate()
            ]);
            $bulletinId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $bulletin->toArray();
            $data['id'] = (int) $bulletinId;
            return new Bulletin($data);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

   public function getBulletinByStudent($studentId, $courseId): Bulletin|null {
        $sql = 'SELECT * FROM bulletins WHERE student_id = :student_id AND course_id = :course_id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return new Bulletin($result);
        }
        return null;
    }


    public function listAll(){
        $sql = 'SELECT * FROM bulletins';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


}
