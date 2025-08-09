<?php

namespace App\Controllers; // <--- THIS MUST BE EXACTLY 'App\Controllers'

use App\Services\ParentService;
use Core\Db;

class ParentController
{
    private ParentService $parentService;

    public function __construct(?ParentService $parentService = null)
    {
        if ($parentService === null) {
            $pdo = Db::connection();
            $this->parentService = new ParentService($pdo);
        } else {
            $this->parentService = $parentService;
        }
    }


    public function listParents()
    {
        $parents = $this->parentService->listParents();
        return $parents;
    }

    public function getParentById($id)
    {
        $parent = $this->parentService->getParentById($id);
        return $parent;
    }


    public function delete($id)
    {
        $result = $this->parentService->delete($id);
        return $result;
    }
}
