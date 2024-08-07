<?php
require_once('../models/estados.models.php');
require_once('../config/cors.php');

$estados = new Clase_Estados();
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $estados->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(array("error" => "No se encontraron estados."));
                }
                break;
                
            case "insertar":
                if (isset($data["descripcion_estado"])) {
                    $descripcion_estado = $data["descripcion_estado"];
                    $resultado = $estados->insertar($descripcion_estado);
                    if ($resultado === "ok") {
                        http_response_code(201); // Created
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el estado: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el estado."));
                }
                break;

            case "actualizar":
                if (isset($data["id_estado"], $data["descripcion_estado"])) {
                    $id_estado = $data["id_estado"];
                    $descripcion_estado = $data["descripcion_estado"];
                    $resultado = $estados->actualizar($id_estado, $descripcion_estado);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el estado: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el estado."));
                }
                break;

            case "eliminar":
                if (isset($data["id_estado"])) {
                    $id_estado = $data["id_estado"];
                    $resultado = $estados->eliminar($id_estado);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el estado: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_estado' para eliminar el estado."));
                }
                break;

            case "detalle":
                if (isset($data["id_estado"])) {
                    $id_estado = $data["id_estado"];
                    try {
                        $estadoDetalle = $estados->buscarPorId($id_estado);
                        if ($estadoDetalle) {
                            http_response_code(200); // OK
                            echo json_encode($estadoDetalle);
                        } else {
                            http_response_code(404); // Not Found
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el estado."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del estado: " . $e->getMessage());
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del estado."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del estado."));
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
