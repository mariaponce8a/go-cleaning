<?php
require_once('../models/descuentos.model.php');
require_once('../config/cors.php');

$descuento = new Clase_Descuentos();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $descuento->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(array("error" => "No se encontraron descuentos."));
                }
                break;

            case "insertar":
                if (isset($data["tipo_descuento_desc"], $data["cantidad_descuento"])) {
                    $tipo_descuento_desc = $data["tipo_descuento_desc"];
                    $cantidad_descuento = $data["cantidad_descuento"];

                    $resultado = $descuento->insertar($tipo_descuento_desc, $cantidad_descuento);
                    if ($resultado === "ok") {
                        http_response_code(201); // Created
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el descuento: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el descuento."));
                }
                break;

            case "actualizar":
                if (isset($data["id_tipo_descuento"], $data["tipo_descuento_desc"], $data["cantidad_descuento"])) {
                    $id_tipo_descuento = $data["id_tipo_descuento"];
                    $tipo_descuento_desc = $data["tipo_descuento_desc"];
                    $cantidad_descuento = $data["cantidad_descuento"];

                    $resultado = $descuento->actualizar($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el descuento: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el descuento."));
                }
                break;

            case "eliminar":
                if (isset($data["id_tipo_descuento"])) {
                    $id_tipo_descuento = $data["id_tipo_descuento"];
                    $resultado = $descuento->eliminar($id_tipo_descuento);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el descuento: " . $resultado));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_tipo_descuento' para eliminar el descuento."));
                }
                break;

            case "detalle":
                if (isset($data["id_tipo_descuento"])) {
                    $id_tipo_descuento = $data["id_tipo_descuento"];
                    try {
                        $descuentoDetalle = $descuento->buscarPorId($id_tipo_descuento);
                        if ($descuentoDetalle) {
                            http_response_code(200); // OK
                            echo json_encode($descuentoDetalle);
                        } else {
                            http_response_code(404); // Not Found
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el descuento."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del descuento: " . $e->getMessage());
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del descuento."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del descuento."));
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
