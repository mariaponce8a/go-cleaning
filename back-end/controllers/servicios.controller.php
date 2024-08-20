<?php
require_once('../models/servicios.model.php');
require_once('../config/cors.php');

$servicio = new Clase_Servicios();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $servicio->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(array("error" => "No se encontraron servicios."));
                }
                break;

            case "insertar":
                if (isset($data["descripcion_servicio"])) {
                    $descripcion_servicio = $data["descripcion_servicio"];
                    $costo_unitario = isset($data["costo_unitario"]) ? $data["costo_unitario"] : null;
                    $validar_pesaje = isset($data["validar_pesaje"]) ? $data["validar_pesaje"] : null;

                    $resultado = $servicio->insertar($descripcion_servicio, $costo_unitario, $validar_pesaje);
                    if ($resultado === "ok") {
                        http_response_code(201); // Created
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el servicio: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el servicio."));
                }
                break;

            case "actualizar":
                if (isset($data["id_servicio"], $data["descripcion_servicio"])) {
                    $id_servicio = $data["id_servicio"];
                    $descripcion_servicio = $data["descripcion_servicio"];
                    $costo_unitario = isset($data["costo_unitario"]) ? $data["costo_unitario"] : null;
                    $validar_pesaje = isset($data["validar_pesaje"]) ? $data["validar_pesaje"] : null;

                    $resultado = $servicio->actualizar($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el servicio: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el servicio."));
                }
                break;

            case "eliminar":
                if (isset($data["id_servicio"])) {
                    $id_servicio = $data["id_servicio"];
                    $resultado = $servicio->eliminar($id_servicio);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el servicio: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_servicio' para eliminar el servicio."));
                }
                break;

            case "detalle":
                if (isset($data["id_servicio"])) {
                    $id_servicio = $data["id_servicio"];
                    try {
                        $servicioDetalle = $servicio->buscarPorId($id_servicio);
                        if ($servicioDetalle) {
                            http_response_code(200); // OK
                            echo json_encode($servicioDetalle);
                        } else {
                            http_response_code(404); // Not Found
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el servicio."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del servicio: " . $e->getMessage());
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del servicio."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del servicio."));
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
