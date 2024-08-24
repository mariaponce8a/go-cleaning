<?php
require_once('./back-end/models/asignaciones_empleado.models.php');

class AsignacionesEmpleado_controller
{
    public function getAllAssignments()
    {
        error_log("Obteniendo todas las asignaciones");
        $asignacionesModel = new Clase_AsignacionesEmpleado();
        $resultado = $asignacionesModel->todos();
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontraron asignaciones."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Asignaciones cargadas con éxito", "data" => $resultado));
        }
    }

    public function insertAssignment($usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado)
    {
        error_log("Insertando una nueva asignación");
        $asignacionesModel = new Clase_AsignacionesEmpleado();
        if ($usuario === null || $fecha_inicio === null || $fecha_fin === null || $id_pedido_cabecera === null || $descripcion_estado === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Faltan parámetros para insertar la asignación."));
        }
        $resultado = $asignacionesModel->insertar($usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Asignación insertada con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al insertar la asignación: " . $resultado));
        }
    }

    public function updateAssignment($id_asignaciones, $usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado)
    {
        error_log("Actualizando la asignación con ID: " . $id_asignaciones);
        $asignacionesModel = new Clase_AsignacionesEmpleado();
        if ($id_asignaciones === null || $usuario === null || $fecha_inicio === null || $fecha_fin === null || $id_pedido_cabecera === null || $descripcion_estado === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Faltan parámetros para actualizar la asignación."));
        }
        $resultado = $asignacionesModel->actualizar($id_asignaciones, $usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Asignación actualizada con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al actualizar la asignación: " . $resultado));
        }
    }

    public function deleteAssignment($id_asignaciones)
    {
        error_log("Eliminando la asignación con ID: " . $id_asignaciones);
        $asignacionesModel = new Clase_AsignacionesEmpleado();
        if ($id_asignaciones === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro 'id_asignaciones' para eliminar la asignación."));
        }
        $resultado = $asignacionesModel->eliminar($id_asignaciones);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Asignación eliminada con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al eliminar la asignación: " . $resultado));
        }
    }

    public function getAssignmentDetail($id_asignaciones)
    {
        error_log("Obteniendo detalle de la asignación con ID: " . $id_asignaciones);
        $asignacionesModel = new Clase_AsignacionesEmpleado();
        if ($id_asignaciones === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro ID para obtener el detalle de la asignación."));
        }
        $resultado = $asignacionesModel->buscarPorId($id_asignaciones);
        if ($resultado === false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró la asignación."));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Asignación encontrada", "data" => $resultado));
        }
    }
}

