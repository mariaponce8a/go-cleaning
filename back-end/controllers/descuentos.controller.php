<?php 
require_once('./back-end/models/descuentos.model.php'); 

class Descuentos_controller {
    
    // Método para obtener todos los descuentos
    public function obtenerTodosDescuentos() {
        $descuentosModel = new Clase_Descuentos();
        $resultado = $descuentosModel->todos();

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se pudieron obtener los descuentos."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }

    // Método para insertar un nuevo descuento
    public function insertarDescuento($tipo_descuento_desc, $cantidad_descuento) {
        $descuentosModel = new Clase_Descuentos();

        // Verificar que los parámetros sean válidos
        if (empty($tipo_descuento_desc) || !is_numeric($cantidad_descuento)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "El tipo de descuento y la cantidad de descuento son requeridos."));
        }

        // Verificar que cantidad_descuento esté en el rango permitido
        if ($cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
            return json_encode(array("respuesta" => "0", "mensaje" => "La cantidad de descuento debe estar entre -9.99 y 9.99."));
        }

        $resultado = $descuentosModel->insertar($tipo_descuento_desc, $cantidad_descuento);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento insertado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para actualizar un descuento
    public function actualizarDescuento($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento) {
        $descuentosModel = new Clase_Descuentos();

        // Verificar que los parámetros sean válidos
        if (empty($id_tipo_descuento) || empty($tipo_descuento_desc) || !is_numeric($cantidad_descuento)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos."));
        }

        // Verificar que cantidad_descuento esté en el rango permitido
        if ($cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
            return json_encode(array("respuesta" => "0", "mensaje" => "La cantidad de descuento debe estar entre -9.99 y 9.99."));
        }

        $resultado = $descuentosModel->actualizar($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento actualizado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para eliminar un descuento
    public function eliminarDescuento($id_tipo_descuento) {
        $descuentosModel = new Clase_Descuentos();

        if (empty($id_tipo_descuento)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de descuento."));
        }

        $resultado = $descuentosModel->eliminar($id_tipo_descuento);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Descuento eliminado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para buscar un descuento por ID
    public function buscarDescuentoPorId($id_tipo_descuento) {
        $descuentosModel = new Clase_Descuentos();

        if (empty($id_tipo_descuento)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de descuento."));
        }

        $resultado = $descuentosModel->buscarPorId($id_tipo_descuento);

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró el descuento."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }

    // Método para buscar descuentos por nombre
    public function buscarDescuentosPorNombre($tipo_descuento_desc) {
        $descuentosModel = new Clase_Descuentos();

        if (empty($tipo_descuento_desc)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un tipo de descuento."));
        }

        $resultado = $descuentosModel->buscarPorNombre($tipo_descuento_desc);

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontraron descuentos con ese nombre."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }
}
?>
