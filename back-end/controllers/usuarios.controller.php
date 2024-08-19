<?php 
require_once('./back-end/models/usuarios.model.php'); 
 
class Usuarios_controller{
    public function validateLogin($usuario,$clave){
        error_log("--------------");
        $usuarioModel = new usuarios_model();
            if($usuario === null  || $clave === null){ 
                return json_encode(array("respuesta" => "0", "mensaje"=> "Debe ingresar sus credenciales" ));
            } 
            $resultado = $usuarioModel->iniciarSesion($usuario,$clave);
            if ($resultado === "Usuario o clave incorrectos.") {
                return json_encode(array("respuesta" => "0", "mensaje"=> $resultado ));
            } else {
                return json_encode(array("respuesta" => "1", "mensaje"=> "OK" ,"data" => json_decode($resultado)));
            } 

    }
}
