<?php

namespace App\Abstracts;

use App\Models\Bulletin;
use App\Models\Student;
use App\Models\Course;
use App\Models\Note;
use App\Interfaces\INoteService;
use PDO;
use Core\Db; // Assuming Core\Db for connection

abstract class AbstractBulletinGenerator
{
    protected PDO $pdo;
    protected INoteService $noteService;

    public function __construct(INoteService $noteService)
    {
        $this->pdo = Db::connection(); // Get PDO connection
        $this->noteService = $noteService;
    }

    /**
     * Template Method: Generates and saves a bulletin for a student in a specific course.
     */
    public final function generateAndSaveBulletin(Student $student, Course $course): Bulletin
    {
        // 1. Collect grades for the student in the given course/subject
        $grades = $this->collectGrades($student->getId(), $course->getId());

        // 2. Calculate average
        $average = $this->calculateAverage($grades);

        // 3. Determine grade letter
        $gradeLetter = $this->determineGradeLetter($average);

        // 4. Add comments (hook method)
        $comments = $this->addComments($student, $course, $average);

        // 5. Create Bulletin object
        $bulletinData = [
            'student_id' => $student->getId(),
            'course_id' => $course->getId(),
            'general_average' => $average,
            'grade_letter' => $gradeLetter,
            'generation_date' => date('Y-m-d H:i:s'),
            'comments' => $comments // If you add comments column to bulletin table
        ];
        $bulletin = new Bulletin($bulletinData);

        // 6. Save the bulletin
        return $this->saveBulletin($bulletin);
    }

    /**
     * Step 1: Collect grades for a student in a specific course/subject.
     * Abstract method to be implemented by concrete generators.
     * @return Note[]
     */
    protected abstract function collectGrades(int $studentId, int $courseId): array;

    /**
     * Step 2: Calculate the average from a list of grades.
     * Can be a concrete method or abstract. Making it concrete here for simplicity.
     * @param Note[] $grades
     */
    protected function calculateAverage(array $grades): float
    {
        if (empty($grades)) {
            return 0.0;
        }
        $total = array_sum(array_map(fn(Note $note) => $note->getValue(), $grades));
        return $total / count($grades);
    }

    /**
     * Step 3: Determine the grade letter based on the average.
     * Can be a concrete method or abstract. Making it concrete here for simplicity.
     */
    protected function determineGradeLetter(float $average): string
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'F';
    }

    /**
     * Step 4 (Hook Method): Add specific comments to the bulletin.
     * Can be overridden by concrete generators.
     */
    protected function addComments(Student $student, Course $course, float $average): string
    {
        return "Bulletin généré pour {$student->getFirstName()} {$student->getLastName()} en {$course->getTitle()}.";
    }

    /**
     * Step 5: Save the generated bulletin to the database.
     * Abstract method to be implemented by concrete generators.
     */
    protected abstract function saveBulletin(Bulletin $bulletin): Bulletin;
}
