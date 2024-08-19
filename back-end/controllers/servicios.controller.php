<?php 
require_once('./back-end/models/servicios.model.php'); 

class Servicios_controller {

    // Método para obtener todos los servicios
    public function obtenerTodosServicios() {
        $serviciosModel = new Clase_Servicios();
        $resultado = $serviciosModel->todos();

        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => $data));
        }
    }

    // Método para insertar un nuevo servicio
    public function insertarServicio($descripcion_servicio, $costo_unitario, $validar_pesaje) {
        $serviciosModel = new Clase_Servicios();

        if (empty($descripcion_servicio) || !is_numeric($costo_unitario) || !is_numeric($validar_pesaje)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos y deben ser válidos."));
        }

        $resultado = $serviciosModel->insertar($descripcion_servicio, $costo_unitario, $validar_pesaje);
        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => $data['mensaje']));
        }
    }

    // Método para actualizar un servicio
    public function actualizarServicio($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje) {
        $serviciosModel = new Clase_Servicios();

        if (empty($id_servicio) || empty($descripcion_servicio) || !is_numeric($costo_unitario) || !is_numeric($validar_pesaje)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos y deben ser válidos."));
        }

        $resultado = $serviciosModel->actualizar($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje);
        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => $data['mensaje']));
        }
    }

    // Método para eliminar un servicio
    public function eliminarServicio($id_servicio) {
        $serviciosModel = new Clase_Servicios();

        if (empty($id_servicio)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de servicio."));
        }

        $resultado = $serviciosModel->eliminar($id_servicio);
        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => $data['mensaje']));
        }
    }

    // Método para buscar un servicio por ID
    public function buscarServicioPorId($id_servicio) {
        $serviciosModel = new Clase_Servicios();

        if (empty($id_servicio)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de servicio."));
        }

        $resultado = $serviciosModel->buscarPorId($id_servicio);
        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => $data));
        }
    }

    // Método para buscar servicios por descripción
    public function buscarServiciosPorDescripcion($descripcion_servicio) {
        $serviciosModel = new Clase_Servicios();

        if (empty($descripcion_servicio)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar una descripción de servicio."));
        }

        $resultado = $serviciosModel->buscarPorDescripcion($descripcion_servicio);
        $data = json_decode($resultado, true);

        if (isset($data['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => $data['error']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => $data));
        }
    }
}
?>
