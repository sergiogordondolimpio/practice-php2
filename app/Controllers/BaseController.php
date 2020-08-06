<?php

/*
Twig es una libreria que nos hace mas segura la pagina,
un Template Engine
composer require twig/twig

hay que hacer extends en donde lo utiliamos en este caso
en la clase EmployeesController

y cambiar la extension del view de php a twig, 
en este caso en form.twig
*/

namespace App\Controllers;

use \Twig_Loader_Filesystem;
use Zend\Diactoros\Response\HtmlResponse;

class BaseController {
    protected $templateEngine;

    public function __construct(){
        $loader = new \Twig\Loader\FilesystemLoader('../views');

        $this->templateEngine = new \Twig\Environment($loader, array(
            'debug' => true,
            'cache' => false,
        ));
    }

    public function renderHTML ($fileName, $data = []){
        return new HtmlResponse($this->templateEngine->render($fileName, $data));   
    }


}