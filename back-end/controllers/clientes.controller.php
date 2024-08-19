<?php 
require_once('./back-end/models/clientes.model.php'); 

class Clientes_controller {
    
    // Método para validar la existencia de un cliente
    public function validarCliente($id_cliente) {
        $clientesModel = new Clase_Clientes();
        
        if ($id_cliente === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de cliente."));
        }

        $resultado = $clientesModel->buscarPorId($id_cliente);

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró el cliente."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }

    // Método para obtener todos los clientes
    public function obtenerTodosClientes() {
        $clientesModel = new Clase_Clientes();
        $resultado = $clientesModel->todos();

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se pudieron obtener los clientes."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }

    // Método para insertar un nuevo cliente
    public function insertarCliente($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente) {
        $clientesModel = new Clase_Clientes();

        if (empty($identificacion_cliente) || empty($tipo_identificacion_cliente) || empty($nombre_cliente) || empty($apellido_cliente) || empty($telefono_cliente) || empty($correo_cliente)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos."));
        }

        $resultado = $clientesModel->insertar($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente insertado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para actualizar un cliente
    public function actualizarCliente($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente) {
        $clientesModel = new Clase_Clientes();

        if (empty($id_cliente) || empty($identificacion_cliente) || empty($tipo_identificacion_cliente) || empty($nombre_cliente) || empty($apellido_cliente) || empty($telefono_cliente) || empty($correo_cliente)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos."));
        }

        $resultado = $clientesModel->actualizar($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente actualizado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para eliminar un cliente
    public function eliminarCliente($id_cliente) {
        $clientesModel = new Clase_Clientes();

        if (empty($id_cliente)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un ID de cliente."));
        }

        $resultado = $clientesModel->eliminar($id_cliente);

        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente eliminado con éxito."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    // Método para buscar clientes por nombre
    public function buscarClientesPorNombre($nombre_cliente) {
        $clientesModel = new Clase_Clientes();

        if (empty($nombre_cliente)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar un nombre de cliente."));
        }

        $resultado = $clientesModel->buscarPorNombre($nombre_cliente);

        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontraron clientes con ese nombre."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        }
    }
}
?>
