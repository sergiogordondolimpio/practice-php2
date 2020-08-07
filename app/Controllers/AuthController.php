<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\RedirectResponse;

use App\Models\User;

class AuthController extends BaseController{
    public function getLogin(){
       
        return $this->renderHTML('login.twig');
    }

    public function postLogin($request){
        $postData = $request->getParsedBody();
            $responseMessage = null;
        //validamos el usuario, primero lo obtenemos de la tabla
        //de la base de datos
        $user = User::where('name', $postData['name'])->first();
        //entra si hay algo
        if ($user){
            if(password_verify($postData['password'], $user->password)){
                //variable superglobal para verificar el usuario
                $_SESSION['userId'] = $user->id; 
                //es para redireccionar
                return new RedirectResponse('/admin');
            }else{
                $responseMessage = 'Bad credentials';
            }
        }else{
                $responseMessage = 'Bad credentials';
        }

        return $this->renderHTML('login.twig', [
            'responseMessage' => $responseMessage
            ]);
    }

    public function getLogout(){
        unset($_SESSION['userId']); 
        return new RedirectResponse('/login');
    }
}