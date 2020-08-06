<?php
// para validar datos usamos respect validation
//se instala con:
//composer require respect/validation

namespace App\Controllers;
// as es para darle un alias
use Respect\Validation\Validator as v;

use App\Models\Employee;

class EmployeesController extends BaseController{
    public function getAddEmployeeAction($request){
        $responseMessage = null;
        
        //getMethod() obtiene el metodo get, post, etc
        if($request->getMethod() == 'POST'){
            //getParseBody() obtiene los datos del formulario
            $postData = $request->getParsedBody();

            //para asignar las validaciones
            $employeeValidator = v::key('name', v::stringType()->notEmpty())
                ->key('dob', v::stringType()->notEmpty());
            
            try{
                //evaluamos si el $postData es valido
                $employeeValidator->assert($postData);
                $employee = new Employee();
                $employee->name = $postData['name'];
                $employee->gender = $postData['gender'];
                $employee->department = $postData['department'];
                $employee->dob = $postData['dob'];

                /*  //para subir archivos, ver documentacion de PSR7
                $files = $request->getUpLoadedFiles();
                    //acceder al objeto, se encuentra en el formulario html en form.twig
                $fileToUpload = $files['fileToUpload'] 
                    //lo ubicamos en una carpeta, lo mejor en otro servidor
                if ($fileToUpload->getError() == UPLOAD_ERR_OK){
                    $fileName = $fileToUpload->getClientFilename();
                        //lo pasa al directorio, primero esta como en memoria
                    $fileToUpload->moveTo('nombreDirectorio/$fileName');
                        //para guardar el nombre del archivo en la base de datos
                    $employee->fileName = $fileName;
                }
                */
                
                $employee->save();

                $responseMessage = 'Saved';
            }catch(\Exception $e){
                $responseMessage = $e->getMessage();
            }
            
        }

        return $this->renderHTML('form.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}