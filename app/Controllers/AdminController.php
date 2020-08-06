<?php

namespace App\Controllers;

use App\Models\Employee;

// se extiende de baseController para acceder a renderHTML()
class AdminController extends BaseController{
    public function getIndex(){
        return $this->renderHTML('admin.twig');
    }

}