<?php

namespace App\Controllers;

use App\Models\Note;
use App\Services\NoteService;
use Core\Db;
use PDO; 

class NoteController
{
    private $pdo;
    private $noteService;
    public function __construct(NoteService $noteService = null)
    {
       $this->pdo = Db::connection();
       $this->noteService = $noteService ?? new NoteService($this->pdo);
        
    }


    public function add(Note $note){
       $note= $this->noteService->add($note);
       return $note;
    }

    public function update(Note $note){
       $note= $this->noteService->update($note);
       return $note;
    }

    public function delete($id){
       return $this->noteService->delete($id);
    }


    public function getGradesByStudent($studentId){
       $studentNotes = $this->noteService->getGradesByStudent($studentId);
       return $studentNotes;
    }

    public function getGradesByEvaluation($evaluationId){
       $evaluationNotes = $this->noteService->getGradesByEvaluation($evaluationId);
       return $evaluationNotes;
    }
   

}
