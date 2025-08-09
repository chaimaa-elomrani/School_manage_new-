<?php
namespace App\Controllers;
use App\Services\ClasseService;
use App\Models\Classe;
use Core\Db;

class ClasseController
{
    private $ClasseService;
    public function __construct(ClasseService $ClasseService = null)
    {
        if ($ClasseService) {
            $this->ClasseService = $ClasseService;
        } else {
            $pdo = Db::connection();
            $this->ClasseService = new ClasseService($pdo);
        }
    }


    public function create(){
        $classe = new Classe();
        $result = $this->ClasseService->create($classe);
        return $result;
    }

    public function listClasses(){
        $classes = $this->ClasseService->listClasses();
        return $classes;
    }

    public function getClassById($id){
        $classe = $this->ClasseService->getClassById($id);
        return $classe;
    }

    public function delete($id){
        $classe = $this->ClasseService->delete($id);
        return $classe;
    }



    public function getAvailableClasses($date, $startTime, $endTime){
        $classes = $this->ClasseService->getAvailableClasses($date, $startTime, $endTime);
        return $classes;
    }
}
