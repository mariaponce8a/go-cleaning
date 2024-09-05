<?php
require_once('./back-end/models/clientes.model.php');

class Clientes_controller     
{
    public function getAllClientes()
    {
        error_log("--------------");
        $clienteModel = new Clase_Clientes();
        $resultado = $clienteModel->getAllClientes();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado === false || empty($resultado)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al cargar los clientes"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Clientes cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function insertCliente($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        // Registrar un log inicial
        error_log("--------------");
    
        // Crear una instancia del modelo de clientes
        $clienteModel = new Clase_Clientes();
    
        // Validar que todos los campos requeridos estén presentes
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
    
        // Intentar registrar el cliente a través del modelo
        $resultado = $clienteModel->registrarCliente(
            $identificacion_cliente,
            $tipo_identificacion_cliente,
            $nombre_cliente,
            $apellido_cliente,
            $telefono_cliente,
            $correo_cliente
        );
    
        // Registrar el resultado de la operación en el log
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
    
        // Verificar si el registro fue exitoso y devolver la respuesta correspondiente
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al registrar el cliente"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente registrado con éxito"));
        }
    }
    
    public function updateCliente($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        error_log("--------------");
        $clienteModel = new Clase_Clientes();
        error_log("------------------------------------------------------ id: " . $id_cliente. 
          " identificacion_cliente: " . $identificacion_cliente . 
          " tipo_identificacion_cliente: " . $tipo_identificacion_cliente . 
          "nombre_cliente: " . $nombre_cliente . 
          " apellido_cliente: " . $apellido_cliente . 
          " telefono_cliente: " . $telefono_cliente . 
          " correo_cliente: " . $correo_cliente);
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
        $resultado = $clienteModel->actualizarCliente(
            $id_cliente,
            $identificacion_cliente,
            $tipo_identificacion_cliente,
            $nombre_cliente,
            $apellido_cliente,
            $telefono_cliente,
            $correo_cliente
        );
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al actualizar el cliente"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente actualizado con éxito"));
        }
    }

    public function deleteCliente($id_cliente)
    {
        error_log("--------------");
        $clienteModel = new Clase_Clientes();
        if ($id_cliente === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "ID de cliente no proporcionado"));
        }
        $resultado = $clienteModel->eliminarCliente($id_cliente);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al eliminar el cliente"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Cliente eliminado con éxito"));
        }
    }

    public function buscarClientePorNombre($nombre_cliente)
    {
        error_log("--------------");
        $clienteModel = new Clase_Clientes();
        if ($nombre_cliente === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Nombre del cliente no proporcionado"));
        }
        $resultado = $clienteModel->buscarClientePorNombre($nombre_cliente);
        error_log("----------RESULTADO SEARCH DESDE CONTROLLER: " . $resultado);
        if (json_decode($resultado)->error) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al buscar los clientes por nombre"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Clientes encontrados con éxito", "data" => json_decode($resultado)));
        }
    }
}
?>
