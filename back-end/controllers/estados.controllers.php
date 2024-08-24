<?php
require_once('./back-end/models/estados.models.php');

class Estados_controller
{
    public function getAllStates()
    {
        error_log("Obteniendo todos los estados");
        $estadosModel = new Clase_Estados();
        $resultado = $estadosModel->todos();
        if (
            $resultado === false
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontraron estados."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Estados cargados con éxito", "data" => $resultado));
        }
    }

    public function insertState($descripcion_estado)
    {
        error_log("Insertando un nuevo estado");
        $estadosModel = new Clase_Estados();
        if (
            $descripcion_estado === null
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Faltan parámetros para insertar el estado."));
        }
        $resultado = $estadosModel->insertar($descripcion_estado);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Estado insertado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al insertar el estado: " . $resultado));
        }
    }

    public function updateState($id_estado, $descripcion_estado)
    {
        error_log("Actualizando el estado con ID: " . $id_estado);
        $estadosModel = new Clase_Estados();
        if (
            $id_estado === null || 
            $descripcion_estado === null
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Faltan parámetros para actualizar el estado."));
        }
        $resultado = $estadosModel->actualizar($id_estado, $descripcion_estado);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Estado actualizado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al actualizar el estado: " . $resultado));
        }
    }

    public function deleteState($id_estado)
    {
        error_log("Eliminando el estado con ID: " . $id_estado);
        $estadosModel = new Clase_Estados();
        if (
            $id_estado === null
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro 'id_estado' para eliminar el estado."));
        }
        $resultado = $estadosModel->eliminar($id_estado);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Estado eliminado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al eliminar el estado: " . $resultado));
        }
    }

    public function getStateDetail($id_estado)
    {
        error_log("Obteniendo detalle del estado con ID: " . $id_estado);
        $estadosModel = new Clase_Estados();
        if (
            $id_estado === null
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro ID para obtener el detalle del estado."));
        }
        $resultado = $estadosModel->buscarPorId($id_estado);
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró el estado."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Estado encontrado", "data" => $resultado));
        }
    }
}