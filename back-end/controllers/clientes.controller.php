<?php
require_once('../models/clientes.model.php');
require_once('../config/cors.php');

$cliente = new Clase_Clientes();
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $cliente->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
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
                        http_response_code(201); // Created
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el cliente: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
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
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el cliente: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el cliente."));
                }
                break;

            case "eliminar":
                if (isset($data["id_cliente"])) {
                    $id_cliente = $data["id_cliente"];
                    $resultado = $cliente->eliminar($id_cliente);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el cliente: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_cliente' para eliminar el cliente."));
                }
                break;

            case "detalle":
                if (isset($data["id_cliente"])) {
                    $id_cliente = $data["id_cliente"];
                    try {
                        $clienteDetalle = $cliente->buscarPorId($id_cliente);
                        if ($clienteDetalle) {
                            http_response_code(200); // OK
                            echo json_encode($clienteDetalle);
                        } else {
                            http_response_code(404); // Not Found
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el cliente."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del cliente: " . $e->getMessage());
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del cliente."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del cliente."));
                }
                break;

            default:
                http_response_code(400); // Bad Request
                echo json_encode(array("resultado" => "error", "error" => "Operación no válida."));
                break;
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("resultado" => "error", "error" => "No se especificó la operación."));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("resultado" => "error", "error" => "Método no permitido."));
}
?>
