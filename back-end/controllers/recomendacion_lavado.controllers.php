<?php
require_once('../models/recomendacion_lavado.models.php');
require_once('../config/cors.php');

$recomendacionLavado = new Clase_RecomendacionLavado();
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $recomendacionLavado->todos();
                if ($datos !== false) {
                    http_response_code(200); // OK
                    echo json_encode($datos);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(array("status" => "error", "message" => "No se encontraron recomendaciones."));
                }
                break;

            case 'insertar':
                if (isset($data['descripcion_material'], $data['descripcion_servicio'])) {
                    $descripcion_material = $data['descripcion_material'];
                    $descripcion_servicio = $data['descripcion_servicio'];

                    try {
                        $resultado = $recomendacionLavado->insertar($descripcion_material, $descripcion_servicio);
                        if ($resultado === "ok") {
                            http_response_code(201); // Created
                            echo json_encode(["status" => "ok"]);
                        } else {
                            throw new Exception($resultado);
                        }
                    } catch (Exception $e) {
                        http_response_code(400); // Bad Request
                        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(["status" => "error", "message" => "Faltan parámetros para insertar los datos."]);
                }
                break;
                
            case 'actualizar':
                if (isset($data['id_recomendacion_lavado'], $data['descripcion_material'], $data['descripcion_servicio'])) {
                    $id_recomendacion_lavado = $data['id_recomendacion_lavado'];
                    $descripcion_material = $data['descripcion_material'];
                    $descripcion_servicio = $data['descripcion_servicio'];
                    try {
                        $resultado = $recomendacionLavado->actualizar($id_recomendacion_lavado, $descripcion_material, $descripcion_servicio);
                        if ($resultado === "ok") {
                            http_response_code(200); // OK
                            echo json_encode(["status" => "ok"]);
                        } else {
                            throw new Exception($resultado);
                        }
                    } catch (Exception $e) {
                        http_response_code(400); // Bad Request
                        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(["status" => "error", "message" => "Faltan parámetros para la actualización de los datos."]);
                }
                break;

            case 'eliminar':
                if (isset($data["id_recomendacion_lavado"])) {
                    $id_recomendacion_lavado = $data['id_recomendacion_lavado'];
                    $resultado = $recomendacionLavado->eliminar($id_recomendacion_lavado);
                    if ($resultado === "ok") {
                        http_response_code(200); // OK
                        echo json_encode(["status" => "ok"]);
                    } else {
                        http_response_code(400); // Bad Request
                        echo json_encode(["status" => "error", "message" => $resultado]);
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(["status" => "error", "message" => "Falta el parámetro ID para eliminar el registro."]);
                }
                break;

            case 'detalle':
                if (isset($data['id_recomendacion_lavado'])) {
                    $id_recomendacion_lavado = $data['id_recomendacion_lavado'];
                    $resultado = $recomendacionLavado->buscarPorId($id_recomendacion_lavado);
                    if ($resultado) {
                        http_response_code(200); // OK
                        echo json_encode([
                            'id_recomendacion_lavado' => $resultado['id_recomendacion_lavado'],
                            'descripcion_material' => $resultado['descripcion_material'],
                            'descripcion_servicio' => $resultado['descripcion_servicio']
                        ]);
                    } else {
                        http_response_code(404); // Not Found
                        echo json_encode(["status" => "error", "message" => "No se encontró el registro con id $id_recomendacion_lavado"]);
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(["status" => "error", "message" => "Falta el parámetro id_recomendacion_lavado."]);
                }
                break;

            default:
                http_response_code(400); // Bad Request
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "No se especificó la operación."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
