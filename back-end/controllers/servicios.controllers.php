<?php
require_once('../models/servicios.models.php'); // Asegúrate de ajustar la ruta según la estructura de tu proyecto
require_once('../config/cors.php'); // Archivo de configuración CORS

$servicio = new Clase_Servicios(); // Suponiendo que Clase_Servicios es tu clase para manejar servicios

header('Content-Type: application/json'); // Establecer encabezado JSON desde el inicio

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el cuerpo de la solicitud POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que se haya enviado el parámetro 'op'
    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $servicio->todos();
                if ($datos !== false) {
                    echo json_encode($datos);
                } else {
                    echo json_encode(array("error" => "No se encontraron servicios."));
                }
                break;
            case "insertar":
                if (isset($data["descripcion_servicio"], $data["costo_unitario"], $data["validar_pesaje"])) {
                    $descripcion_servicio = $data["descripcion_servicio"];
                    $costo_unitario = $data["costo_unitario"];
                    $validar_pesaje = $data["validar_pesaje"];
                    $resultado = $servicio->insertar($descripcion_servicio, $costo_unitario, $validar_pesaje);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el servicio: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para insertar el servicio."));
                }
                break;
            case "actualizar":
                if (isset($data["id_servicio"], $data["descripcion_servicio"], $data["costo_unitario"], $data["validar_pesaje"])) {
                    $id_servicio = $data["id_servicio"];
                    $descripcion_servicio = $data["descripcion_servicio"];
                    $costo_unitario = $data["costo_unitario"];
                    $validar_pesaje = $data["validar_pesaje"];
    
                    $resultado = $servicio->actualizar($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje);
    
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el servicio: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el servicio."));
                }
                break;
            case "eliminar":
                if (isset($data["id_servicio"])) {
                    $id_servicio = $data["id_servicio"];
                    $resultado = $servicio->eliminar($id_servicio);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el servicio: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_servicio' para eliminar el servicio."));
                }
                break;
            case "detalle":
                if (isset($data["id_servicio"])) {
                    $id_servicio = $data["id_servicio"];
                    try {
                        $servicioDetalle = $servicio->buscarPorId($id_servicio);
                        if ($servicioDetalle) {
                            echo json_encode($servicioDetalle);
                        } else {
                            echo json_encode(array("resultado" => "error", "error" => "No se encontró el servicio."));
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener el detalle del servicio: " . $e->getMessage());
                        echo json_encode(array("resultado" => "error", "error" => "Error al obtener el detalle del servicio."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del servicio."));
                }
                break;
            case "buscarPorDescripcion":
                if (isset($data["descripcion_servicio"])) {
                    $descripcion_servicio = $data["descripcion_servicio"];
                    $serviciosEncontrados = $servicio->buscarPorDescripcion($descripcion_servicio);
                    if ($serviciosEncontrados !== false) {
                        echo json_encode($serviciosEncontrados);
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al buscar servicios por descripción."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'descripcion_servicio' para buscar servicios por descripción."));
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
