<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use PDO;
use Core\Db; // Assuming Core\Db for connection

class ClassroomService
{
    private PDO $pdo;
    private StudentService $studentService;
    private TeacherService $teacherService;

    public function __construct()
    {
        $this->pdo = Db::connection();
        $this->studentService = new StudentService();
        $this->teacherService = new TeacherService();
    }

    public function create(Classroom $classroom): Classroom
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO classrooms (name, academic_year, main_teacher_id, description) VALUES (:name, :academic_year, :main_teacher_id, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $classroom->getName(),
                'academic_year' => $classroom->getAcademicYear(),
                'main_teacher_id' => $classroom->getTeacherId(),
            ]);
            $classroomId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            $data = $classroom->toArray();
            $data['id'] = (int) $classroomId;
            return new Classroom($data);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getById(int $id, bool $withStudents = false): ?Classroom
    {
        $stmt = $this->pdo->prepare('SELECT * FROM classrooms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $classroom = new Classroom($row);
            if ($withStudents) {
                $classroom->setStudents($this->getStudentsByClassroom($id));
            }
            return $classroom;
        }
        return null;
    }

    public function getAll(bool $withStudents = false): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM classrooms ORDER BY academic_year DESC, name ASC');
        $stmt->execute();
        $classrooms = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $classroom = new Classroom($row);
            if ($withStudents) {
                $classroom->setStudents($this->getStudentsByClassroom($classroom->getId()));
            }
            $classrooms[] = $classroom;
        }
        return $classrooms;
    }

    public function update(Classroom $classroom): Classroom
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE classrooms SET name = :name, academic_year = :academic_year, main_teacher_id = :main_teacher_id, description = :description WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $classroom->getId(),
                'name' => $classroom->getName(),
                'academic_year' => $classroom->getAcademicYear(),
                'teacher_id' => $classroom->getTeacherId(),
            ]);
            $this->pdo->commit();
            return $classroom;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Also deletes entries in classroom_students due to CASCADE
            $stmt = $this->pdo->prepare('DELETE FROM classrooms WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Assigns a student to a classroom.
     */
    public function assignStudent(int $classroomId, int $studentId): bool
    {
        // Check if classroom and student exist
        if (!$this->getById($classroomId) || !$this->studentService->getStudentById($studentId)) {
            throw new \InvalidArgumentException("Classroom or Student not found.");
        }

        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO classroom_students (classroom_id, student_id) VALUES (:classroom_id, :student_id) ON CONFLICT (classroom_id, student_id) DO NOTHING";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'classroom_id' => $classroomId,
                'student_id' => $studentId,
            ]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0; // Returns true if a new row was inserted
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Unassigns a student from a classroom.
     */
    public function unassignStudent(int $classroomId, int $studentId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "DELETE FROM classroom_students WHERE classroom_id = :classroom_id AND student_id = :student_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'classroom_id' => $classroomId,
                'student_id' => $studentId,
            ]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Assigns a main teacher to a classroom.
     */
    public function assignTeacher(int $classroomId, int $teacherId): bool
    {
        // Check if classroom and teacher exist
        if (!$this->getById($classroomId) || !$this->teacherService->getTeacherById($teacherId)) {
            throw new \InvalidArgumentException("Classroom or Teacher not found.");
        }

        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE classrooms SET main_teacher_id = :teacher_id WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'teacher_id' => $teacherId,
                'id' => $classroomId,
            ]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Unassigns the main teacher from a classroom.
     */
    public function unassignTeacher(int $classroomId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE classrooms SET main_teacher_id = NULL WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $classroomId]);
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Helper to get students assigned to a specific classroom.
     * @return Student[]
     */
    public  function getStudentsByClassroom(int $classroomId): array
    {
        $sql = "
            SELECT s.id, p.first_name, p.last_name, p.email
            FROM classroom_students cs
            JOIN students s ON cs.student_id = s.id
            JOIN person p ON s.person_id = p.id
            WHERE cs.classroom_id = :classroom_id
            ORDER BY p.last_name, p.first_name
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['classroom_id' => $classroomId]);
        $students = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $students[] = new Student($row);
        }
        return $students;
    }


    public  function getTeacherByClassroom($classroomId){
        $sql = "SELECT t.id, p.first_name, p.last_name, from teachers t join person p on t.person_id = p.id where t.classroom_id = :classroom_id";
        $stmt = $this->pdo->prepare($sql);
        $teacher = $stmt->execute(['classroom_id' => $classroomId]); 
        return $teacher; 
    }
}
