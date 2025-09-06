<?php
require_once('./back-end/models/descuentos.model.php');

class Descuentos_controller
{
    public function getAllDescuentos()
    {
        error_log("--------------");
        $descuentoModel = new Clase_Descuentos();
        $resultado = $descuentoModel->getAllDescuentos();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los descuentos"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuentos cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function insertDescuento($tipo_descuento_desc, $cantidad_descuento)
    {
        error_log("--------------");
        $descuentoModel = new Clase_Descuentos();
        if (
            $tipo_descuento_desc === null ||
            $cantidad_descuento === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $descuentoModel->registrarDescuento($tipo_descuento_desc, $cantidad_descuento);
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el descuento"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento registrado con éxito"));
        }
    }

    public function updateDescuento($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento)
    {
        error_log("--------------");
        $descuentoModel = new Clase_Descuentos();
        if (
            $id_tipo_descuento === null ||
            $tipo_descuento_desc === null ||
            $cantidad_descuento === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $descuentoModel->actualizarDescuento ($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento);
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el descuento"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento actualizado con éxito"));
        }
    }

    public function deleteDescuentos($id_tipo_descuento)
    {
        error_log("--------------");
        $descuentoModel = new Clase_Descuentos();
        if ($id_tipo_descuento === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Descuento no seleccionado"));
        }
        $resultado = $descuentoModel->eliminarDescuento($id_tipo_descuento);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el descuento"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento eliminado con éxito"));
        }
    }

    public function getDescuentoById($id_tipo_descuento)
    {
        error_log("--------------");
        $descuentoModel = new Clase_Descuentos();
        if ($id_tipo_descuento === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "ID de descuento no proporcionado"));
        }
        $resultado = $descuentoModel->getDescuentoById($id_tipo_descuento);
        error_log("----------RESULTADO SEARCH DESDE CONTROLLER: " . $resultado);
        
        if ($resultado !== false) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento encontrado con éxito", "data" => json_decode($resultado)));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró el descuento"));
        }
    }
}
?>
