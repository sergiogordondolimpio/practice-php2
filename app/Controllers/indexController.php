<?php

namespace App\Controllers;

use App\Models\Employee;

// se extiende de baseController para acceder a renderHTML()
class IndexController extends BaseController{
    public function indexAction(){
        $employees = Employee::all();

        $name = 'Employees';

        // hay que pasarle las varables que se quieren mostrar en un arreglo
        return $this->renderHTML('index.twig', [
            'name' => $name,
            'employees' => $employees
        ]);
    }

}