<?php
require_once('./back-end/models/servicios.model.php');

class Servicios_controller
{    
    public function getAllServices()
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        $resultado = $servicioModel->getAllServices();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado === false || empty($resultado)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los servicios"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicios cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function insertService($descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        if (
            $descripcion_servicio === null ||
            $costo_unitario === null ||
            $validar_pesaje === null 
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $servicioModel->registrarServicio ($descripcion_servicio, $costo_unitario, $validar_pesaje);
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el servicio"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicio registrado con éxito"));
        }
    }

    public function updateService($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        if (
            $id_servicio === null ||
            $descripcion_servicio === null ||
            $costo_unitario === null ||
            $validar_pesaje === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $servicioModel->actualizarServicios ($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje);
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el servicio"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicio actualizado con éxito"));
        }
    }

    public function deleteService($id_servicio)
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        if (
            $id_servicio === null
            ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Servicio no seleccionado"));
        }
        $resultado = $servicioModel->eliminarServicios ($id_servicio);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el servicio"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicio eliminado con éxito"));
        }
    }

    public function findServiceById($id_servicio)
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        if ($id_servicio === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "ID de servicio no proporcionado"));
        }
        $resultado = $servicioModel->findServiceById($id_servicio);
        error_log("----------RESULTADO FIND DESDE CONTROLLER: " . $resultado);
        if (json_decode($resultado)->error) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para obtener el detalle del servicio"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicio encontrado con éxito", "data" => json_decode($resultado)));
        }
    }

    public function findServiceByDescription($descripcion_servicio)
    {
        error_log("--------------");
        $servicioModel = new Clase_Servicios();
        if ($descripcion_servicio === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Descripción del servicio no proporcionada"));
        }
        $resultado = $servicioModel->findServiceByDescription($descripcion_servicio);
        error_log("----------RESULTADO FIND DESDE CONTROLLER: " . $resultado);
        if (json_decode($resultado)->error) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para buscar el servicio"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Servicios encontrados con éxito", "data" => json_decode($resultado)));
        }
    }
}
?>
