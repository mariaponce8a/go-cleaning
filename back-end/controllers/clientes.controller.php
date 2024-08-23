<?php
require_once('./back-end/models/clase_clientes.model.php');

class Clientes_controller
{
    private $clienteModel;

    public function constructor()
    {
        $this->clienteModel = new Clase_Clientes();
    }

    public function getAllClientes()
    {
        error_log("--------------");
        $resultado = $this->clienteModel->getAllClientes();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al cargar los clientes"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Clientes cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function registrarCliente($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        error_log("--------------");
        if (
            $identificacion_cliente === null ||
            $tipo_identificacion_cliente === null ||
            $nombre_cliente === null ||
            $apellido_cliente === null ||
            $telefono_cliente === null ||
            $correo_cliente === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        
        $resultado = $this->clienteModel->registrarCliente(
            $identificacion_cliente,
            $tipo_identificacion_cliente,
            $nombre_cliente,
            $apellido_cliente,
            $telefono_cliente,
            $correo_cliente
        );
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente registrado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al registrar el cliente"));
        }
    }

    public function actualizarCliente($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        error_log("--------------");
        if (
            $id_cliente === null ||
            $identificacion_cliente === null ||
            $tipo_identificacion_cliente === null ||
            $nombre_cliente === null ||
            $apellido_cliente === null ||
            $telefono_cliente === null ||
            $correo_cliente === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        
        $resultado = $this->clienteModel->actualizarCliente(
            $id_cliente,
            $identificacion_cliente,
            $tipo_identificacion_cliente,
            $nombre_cliente,
            $apellido_cliente,
            $telefono_cliente,
            $correo_cliente
        );
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente actualizado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al actualizar el cliente"));
        }
    }

    public function eliminarCliente($id_cliente)
    {
        error_log("--------------");
        if ($id_cliente === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "ID de cliente no proporcionado"));
        }
        
        $resultado = $this->clienteModel->eliminarCliente($id_cliente);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente eliminado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al eliminar el cliente"));
        }
    }

    public function buscarClientePorNombre($nombre_cliente)
    {
        error_log("--------------");
        if ($nombre_cliente === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Nombre del cliente no proporcionado"));
        }
        
        $resultado = $this->clienteModel->buscarClientePorNombre($nombre_cliente);
        error_log("----------RESULTADO SEARCH DESDE CONTROLLER: " . $resultado);
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al buscar los clientes por nombre"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Clientes encontrados con éxito", "data" => json_decode($resultado)));
        }
    }
}
