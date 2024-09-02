<?php
require_once('./back-end/models/pedidos.models.php');

class Pedidos_controller
{

    public function getAllPedidos()
    {
        error_log("--------------");
        $model = new pedidos_model();
        $resultado = $model->getAllPedidos();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los pedidos"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedidos cargados con éxito", "data" => json_decode($resultado)));
        }
    }


    // public function insertUser($nombre, $apellido, $perfil, $usuario, $clave) {}


    // public function updateUser($id, $nombre, $apellido, $perfil, $usuario, $clave) {}

    // public function deleteUsuario($id) {}
}
