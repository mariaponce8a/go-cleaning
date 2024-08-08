<?php
require_once('../models/descuentos.models.php'); // Asegúrate de que el archivo existe
require_once('../config/cors.php');

$descuento = new Clase_Descuentos();
header('Content-Type: application/json'); // Establecer encabezado JSON desde el inicio

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos el cuerpo de la solicitud POST
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        echo json_encode(array("resultado" => "error", "error" => "No se pudo decodificar el JSON."));
        exit;
    }

    // Verificamos que se haya enviado el parámetro 'op'
    if (isset($data['op'])) {
        switch ($data['op']) {
            case "todos":
                $datos = $descuento->todos();
                if ($datos !== false) {
                    echo json_encode($datos);
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "No se encontraron descuentos."));
                }
                break;
            case "insertar":
                if (isset($data["tipo_descuento_desc"], $data["cantidad_descuento"])) {
                    $tipo_descuento_desc = $data["tipo_descuento_desc"];
                    $cantidad_descuento = $data["cantidad_descuento"];
                    $resultado = $descuento->insertar($tipo_descuento_desc, $cantidad_descuento);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al insertar el descuento: " . $resultado));
                    }
                } else {
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
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al actualizar el descuento: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Faltan parámetros para actualizar el descuento."));
                }
                break;
            case "eliminar":
                if (isset($data["id_tipo_descuento"])) {
                    $id_tipo_descuento = $data["id_tipo_descuento"];
                    $resultado = $descuento->eliminar($id_tipo_descuento);
                    if ($resultado === "ok") {
                        echo json_encode(array("resultado" => "ok"));
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al eliminar el descuento: " . $resultado));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'id_tipo_descuento' para eliminar el descuento."));
                }
                break;
            case "detalle":
                if (isset($data["id_tipo_descuento"])) {
                    $id_tipo_descuento = $data["id_tipo_descuento"];
                    $descuentoDetalle = $descuento->buscarPorId($id_tipo_descuento);
                    if ($descuentoDetalle) {
                        echo json_encode($descuentoDetalle);
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "No se encontró el descuento."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro ID para obtener el detalle del descuento."));
                }
                break;
            case "buscarPorNombre":
                if (isset($data["tipo_descuento_desc"])) {
                    $tipo_descuento_desc = $data["tipo_descuento_desc"];
                    $descuentosEncontrados = $descuento->buscarPorNombre($tipo_descuento_desc);
                    if ($descuentosEncontrados !== false) {
                        echo json_encode($descuentosEncontrados);
                    } else {
                        echo json_encode(array("resultado" => "error", "error" => "Error al buscar descuentos por nombre."));
                    }
                } else {
                    echo json_encode(array("resultado" => "error", "error" => "Falta el parámetro 'tipo_descuento_desc' para buscar descuentos por nombre."));
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
