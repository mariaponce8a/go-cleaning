<?php
require_once('./back-end/models/usuarios.model.php');

class Usuarios_controller
{
    public function validateLogin($usuario, $clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if ($usuario === null  || $clave === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar sus credenciales"));
        }
        error_log($usuario);
        error_log($clave);
        $resultado = $usuarioModel->iniciarSesion($usuario, $clave);
        if ($resultado === "Usuario o clave incorrectos.") {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }

    public function getAllUsers()
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        $resultado = $usuarioModel->getAllUsers();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los usuarios"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuarios cargados con éxito", "data" => json_decode($resultado)));
        }
    }


    public function insertUser($nombre, $apellido, $perfil, $usuario, $clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if (
            $usuario === null  ||
            $clave === null ||
            $nombre === null  ||
            $apellido === null ||
            $perfil === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $usuarioModel->registrarUsuario($nombre, $apellido, $perfil, $usuario, $clave);
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el usuario"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario registrado con éxito"));
        }
    }


    public function updateUser($id, $nombre, $apellido, $perfil, $usuario, $clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        error_log("------------------------------------------------------ id: " . $id . " nombre: " . $nombre . " apellido: " . $apellido . " perfil: " . $perfil . " usuario: " . $usuario . " clave: " . $clave);
        if (
            $id === null ||
            $usuario === null  ||
            $clave === null ||
            $nombre === null  ||
            $apellido === null ||
            $perfil === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $usuarioModel->actualizarUsuario($id, $nombre, $apellido, $perfil, $usuario, $clave);
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el usuario"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario actualizado con éxito"));
        }
    }

    public function deleteUsuario($id)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if (
            $id === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Usuario no seleccionado"));
        }
        $resultado = $usuarioModel->eliminarUsuario($id);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el usuario"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario eliminado con éxito"));
        }
    }
}
