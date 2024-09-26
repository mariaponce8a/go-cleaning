<?php
require_once('./back-end/models/iva.model.php');

class Iva_controller
{
    public function getAllIvas()
    {
        error_log("Obteniendo todos los rgistros");
        $ivaModel = new Clase_Iva();
        $resultado = $ivaModel->getAllIvas();
        if (
            $resultado === false
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontraron registros."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Ivas cargados con éxito", "data" => $resultado));
        }
    }
}