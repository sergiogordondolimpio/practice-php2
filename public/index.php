<?php

/*
Utilizando composer, una libreria de php
Buscar librerias de composer en https://packagist.org/

Para manejar la base de datos, se installa en comando
composer install illuminate/database

Para usar PSR7 para realizar request y response
composer require zendframework/zend-diactoros
*/

//inicializa las variables, para ver errores
//1 es como true
ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require_once '../app/Models/Employee.php';

//para iniciar la sesion el usurio, puede o no estar logueado
session_start();

/*
Para ingresar a la base de datos sin tener que usar directamente las
variables, sino como encriptadas usamos VLUCAS, una libreria de php
que las buscamos en packagist.org, es en realida un archivo donde estan
las variables de entorno, como si fuera en un servidor 
para instalar: $ composer require vlucas/phpdotenv
*/
//indicamos en que directorio estan las variables, en un archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();



//de la libreria de composer
use Illuminate\Database\Capsule\Manager as Capsule;
use Models\Employee;

/*estamos usando el aura router */
use Aura\Router\RouterContainer;

$capsule = new Capsule;

/*
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'port'      => '3306',
    'database'  => 'crudapi',
    'username'  => 'root',
    'password'  => 'Franc3sca',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
*/

//usando Dotenv
$capsule->addConnection([
    'driver'    =>  $_ENV['DB_DRIVE'],
    'host'      =>  $_ENV['DB_HOST'],
    'port'      =>  $_ENV['DB_PORT'],
    'database'  =>  $_ENV['DB_NAME'],
    'username'  =>  $_ENV['DB_USER'],
    'password'  =>  $_ENV['DB_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);


//creamos un contendor de rutas
$routerContainer = new RouterContainer();

//creamos el mapa
$map = $routerContainer->getMap();
//usamos metodo get, primer parametro el nombre de la ruta
//my importante el manejo de la ruta, index esta modificado por
//.htaccess, / es la direccion inicial, ../index.php
//es el archivo que ejecuta

//saque el practice-php2/ para que trabaje en localhost de postgres
$map->get('index', '/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction'
]);
$map->get('form', '/employee/add', [
    'controller' => 'App\Controllers\EmployeesController',
    'action' => 'getAddEmployeeAction'
]);
$map->post('saveEmployee', '/employee/add', [
    'controller' => 'App\Controllers\EmployeesController',
    'action' => 'getAddEmployeeAction'
]);
$map->get('user', '/user/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUserAction'
]);
$map->post('saveUser', '/user/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUserAction'
]);
$map->get('loginForm', '/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);
$map->get('logout', '/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);
$map->post('auth', '/auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);
$map->get('admin', '/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);

//obtenemos el matcher
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

//funcion para que imprima de la base de datos
function printElements($employee){
    echo '<p>' . $employee->name . '</p>';
}

//no encontro ruta
if (!$route){
    echo 'no route';
}else{
    $handlerdata = $route->handler;
    $controllerName = $handlerdata['controller'];
    $actionName = $handlerdata['action'];
    //lee la variable auth y si no esta definido envia false
    $needsAuth = $handlerdata['auth'] ?? false;

    $sessionUserId = $_SESSION['userId'] ?? null;
    if($needsAuth && !$sessionUserId){
        echo 'Protected route';
        die;
    }

    //el new instancia la clase con el nombre de esa variable
    $controller = new $controllerName;
    //ejecutamos un metodo, basado en una cadena
    $response = $controller->$actionName($request);
    
    foreach($response->getHeaders() as $name => $values)
    {
        foreach($values as $value){
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();
    /* cuando encuetra la ruta, por ejemplo / , handler va a tener un 
    arreglo que esta em $map */
    //var_dump ($route->handler);
}


//para ver la ruta
//var_dump($route);


