<?php
require_once('../models/material.models.php');
require_once('../config/cors.php');

$material = new Clase_Material();
header('Content-Type: application/json'); // Establecer encabezado JSON desde el inicio

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $material->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(array("error" => "No se encontraron materiales."));
                }
                break;
                
            case "insertar":
                if (isset($data["descripcion_material"])) {
                    $descripcion_material = $data["descripcion_material"];
                    $resultado = $material->insertar($descripcion_material);
                    if ($resultado === "ok") {
                        http_response_code(201); // Created
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el material: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el material."));
                }
                break;

            case "actualizar":
                if (isset($data["id_material"], $data["descripcion_material"])) {
                    $id_material = $data["id_material"];
                    $descripcion_material = $data["descripcion_material"];
                    $resultado = $material->actualizar($id_material, $descripcion_material);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el material: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el material."));
                }
                break;

            case "eliminar":
                if (isset($data["id_material"])) {
                    $id_material = $data["id_material"];
                    $resultado = $material->eliminar($id_material);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el material: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_material' para eliminar el material."));
                }
                break;

            case "detalle":
                if (isset($data["id_material"])) {
                    $id_material = $data["id_material"];
                    try {
                        $materialDetalle = $material->buscarPorId($id_material);
                        if ($materialDetalle) {
                            http_response_code(200); // OK
                            echo json_encode($materialDetalle);
                        } else {
                            http_response_code(404); // Not Found
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el material."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del material: " . $e->getMessage());
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del material."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del material."));
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
