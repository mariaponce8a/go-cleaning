<?php
require_once('../models/clientes.models.php');
require_once('../config/cors.php');

$cliente = new Clase_Clientes();
header('Content-Type: application/json'); // Establecer encabezado JSON desde el inicio

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos el cuerpo de la solicitud POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificamos que se haya enviado el parámetro 'op'
    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $cliente->todos();
                if ($datos !== false) {
                    echo json_encode($datos);
                } else {
                    echo json_encode(array("error" => "No se encontraron clientes."));
                }
                break;
            case "insertar":
                if (isset($data["identificacion_cliente"], $data["tipo_identificacion_cliente"], $data["nombre_cliente"], $data["apellido_cliente"], $data["telefono_cliente"], $data["correo_cliente"])) {
                    $identificacion_cliente = $data["identificacion_cliente"];
                    $tipo_identificacion_cliente = $data["tipo_identificacion_cliente"];
                    $nombre_cliente = $data["nombre_cliente"];
                    $apellido_cliente = $data["apellido_cliente"];
                    $telefono_cliente = $data["telefono_cliente"];
                    $correo_cliente = $data["correo_cliente"];
                    $resultado = $cliente->insertar($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el cliente: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el cliente."));
                }
                break;
            case "actualizar":
                if (isset($data["id_cliente"], $data["identificacion_cliente"], $data["tipo_identificacion_cliente"], $data["nombre_cliente"], $data["apellido_cliente"], $data["telefono_cliente"], $data["correo_cliente"])) {
                    $id_cliente = $data["id_cliente"];
                    $identificacion_cliente = $data["identificacion_cliente"];
                    $tipo_identificacion_cliente = $data["tipo_identificacion_cliente"];
                    $nombre_cliente = $data["nombre_cliente"];
                    $apellido_cliente = $data["apellido_cliente"];
                    $telefono_cliente = $data["telefono_cliente"];
                    $correo_cliente = $data["correo_cliente"];
                    $resultado = $cliente->actualizar($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el cliente: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el cliente."));
                }
                break;
            case "eliminar":
                if (isset($data["id_cliente"])) {
                    $id_cliente = $data["id_cliente"];
                    $resultado = $cliente->eliminar($id_cliente);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el cliente: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_cliente' para eliminar el cliente."));
                }
                break;
            case "detalle":
                if (isset($data["id_cliente"])) {
                    $id_cliente = $data["id_cliente"];
                    try {
                        $clienteDetalle = $cliente->buscarPorId($id_cliente);
                        if ($clienteDetalle) {
                            echo json_encode($clienteDetalle);
                        } else {
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el cliente."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del cliente: " . $e->getMessage());
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del cliente."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del cliente."));
                }
                break;
            case "buscarPorNombre":
                if (isset($data["nombre_cliente"])) {
                    $nombre_cliente = $data["nombre_cliente"];
                    $clientesEncontrados = $cliente->buscarPorNombre($nombre_cliente);
                    if ($clientesEncontrados !== false) {
                        echo json_encode($clientesEncontrados);
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al buscar clientes por nombre."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'nombre_cliente' para buscar clientes por nombre."));
                }
                break;
            default:
                echo json_encode(array("resultado" => "error", "error" => "Operación no válida."));
                break;
        }
    } else {
        echo json_encode(array("resultado" => "error", "error" => "No se especificó la operación."));
    }
} else {
    echo json_encode(array("resultado" => "error", "error" => "Método no permitido."));
}
?>
