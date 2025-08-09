<?php
namespace App\Services;
use App\Models\Bulletin;
use App\Interfaces\INoteService;
use App\Abstracts\AbstractBulletinGenerator;

use PDO;

class BulletinService extends AbstractBulletinGenerator
{
    private $pdo;
    private $noteService;

    public function __construct(PDO $pdo, INoteService $noteService)
    {
        $this->pdo = $pdo;
        $this->noteService = $noteService;
    }

    public function save(Bulletin $bulletin)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO bulletins(student_id , course_id , evaluation_id , grade , general_average)
                VALUES (:student_id , :course_id , :evaluation_id , :grade , :general_average)'
            );
            $stmt->execute([
                'student_id' => $bulletin->getStudentId(),
                'course_id' => $bulletin->getCourseId(),
                'evaluation_id' => $bulletin->getEvaluationId(),
                'grade' => $bulletin->getGrade(),
                'general_average' => $bulletin->getGeneralAverage()
            ]);

            $bulletinId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            // Return bulletin with ID
            return new Bulletin([
                'id' => $bulletinId,
                'student_id' => $bulletin->getStudentId(),
                'course_id' => $bulletin->getCourseId(),
                'evaluation_id' => $bulletin->getEvaluationId(),
                'grade' => $bulletin->getGrade(),
                'general_average' => $bulletin->getGeneralAverage()
            ]);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    public function getAll()
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.student_id , b.course_id , b.evaluation_id , b.grade , b.general_average,
         s.person_id , p.first_name , p.last_name FROM bulletins b
         JOIN students s ON b.student_id = s.id
         JOIN person p ON s.person_id = p.id
         JOIN courses c ON b.course_id = c.id
         JOIN evaluations e ON b.evaluation_id = e.id'

        );

        $stmt->execute();
        $bulletins = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $bulletins[] = new Bulletin($row);
        }
        return $bulletins;

    }


    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT  b.student_id , b.course_id , b.evaluation_id , b.grade , b.general_average,
         s.person_id , p.first_name , p.last_name FROM bulletins b
         JOIN students s ON b.student_id = s.id
         JOIN person p ON s.person_id = p.id
         JOIN courses c ON b.course_id = c.id
            JOIN evaluations e ON b.evaluation_id = e.id
            WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Bulletin($row);
        }
        return null;
    }

    public function update(Bulletin $bulletin)
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE bulletins SET student_id = :student_id, course_id = :course_id, evaluation_id = :evaluation_id, grade = :grade, general_average = :general_average WHERE id = :id'
            );
            $stmt->execute([
                'student_id' => $bulletin->getStudentId(),
                'course_id' => $bulletin->getCourseId(),
                'evaluation_id' => $bulletin->getEvaluationId(),
                'grade' => $bulletin->getGrade(),
                'general_average' => $bulletin->getGeneralAverage()
            ]);
            return $bulletin;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM bulletins WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return true;
    }


     public function collectGrades($studentId, $courseId): array
    {
        return $this->noteService->getGradesByStudent($studentId);
    }

    public function calculateAverage(array $grades): float
    {
        if (empty($grades)) {
            return 0.0;
        }

        $total = 0;
        foreach ($grades as $grade) {
            $total += $grade->getScore();
        }

        return $total / count($grades);
    }

    public function determineGrade(float $average): string
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'F';
    }

    
    

}