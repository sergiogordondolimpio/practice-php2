<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;

use App\Models\User;

class UsersController extends BaseController{
    public function getAddUserAction($request){
        $responseMessage = null;
        
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();

            $userValidator = v::key('name', v::stringType()->notEmpty())
                ->key('password', v::stringType()->notEmpty());
            
            try{
                $userValidator->assert($postData);
                $user = new User();
                $user->name = $postData['name'];
                // password_hash encripta el pasword
                $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
                
                $user->save();

                $responseMessage = 'Saved';
            }catch(\Exception $e){
                $responseMessage = $e->getMessage();
            }
            
        }

        return $this->renderHTML('user.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}