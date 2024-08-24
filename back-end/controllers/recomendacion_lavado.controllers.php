<?php
require_once('./back-end/models/recomendacion_lavado.models.php');

class RecomendacionLavado_controller
{
    public function getAllRecommendations()
    {
        error_log("--------------");
        $recomendacionLavadoModel = new Clase_RecomendacionLavado();
        $resultado = $recomendacionLavadoModel->todos();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . json_encode($resultado));
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar las recomendaciones de lavado"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Recomendaciones cargadas con éxito", "data" => $resultado));
        }
    }

    public function insertRecommendation($descripcion_material, $descripcion_servicio)
    {
        error_log("--------------");
        $recomendacionLavadoModel = new Clase_RecomendacionLavado();
        if ($descripcion_material === null || $descripcion_servicio === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $recomendacionLavadoModel->insertar($descripcion_material, $descripcion_servicio);
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar la recomendación de lavado"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Recomendación registrada con éxito"));
        }
    }

    public function updateRecommendation($id_recomendacion_lavado, $descripcion_material, $descripcion_servicio)
    {
        error_log("--------------");
        $recomendacionLavadoModel = new Clase_RecomendacionLavado();
        if ($id_recomendacion_lavado === null || $descripcion_material === null || $descripcion_servicio === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $recomendacionLavadoModel->actualizar($id_recomendacion_lavado, $descripcion_material, $descripcion_servicio);
        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar la recomendación de lavado"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Recomendación actualizada con éxito"));
        }
    }

    public function deleteRecommendation($id_recomendacion_lavado)
    {
        error_log("--------------");
        $recomendacionLavadoModel = new Clase_RecomendacionLavado();
        if ($id_recomendacion_lavado === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Recomendación no seleccionada"));
        }
        $resultado = $recomendacionLavadoModel->eliminar($id_recomendacion_lavado);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar la recomendación de lavado"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Recomendación eliminada con éxito"));
        }
    }

    public function getRecommendationDetail($id_recomendacion_lavado)
    {
        error_log("--------------");
        $recomendacionLavadoModel = new Clase_RecomendacionLavado();
        if ($id_recomendacion_lavado === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro ID para obtener el detalle de la recomendación de lavado."));
        }
        try {
            $recomendacionDetalle = $recomendacionLavadoModel->buscarPorId($id_recomendacion_lavado);
            error_log("----------RESULTADO DETALLE DESDE CONTROLLER: " . json_encode($recomendacionDetalle));
            if ($recomendacionDetalle == false) {
                return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró la recomendación de lavado."));
            } else {
                return json_encode(array("respuesta" => "1", "mensaje" => "Detalle de la recomendación de lavado cargado con éxito", "data" => $recomendacionDetalle));
            }
        } catch (Exception $e) {
            error_log("Error al obtener el detalle de la recomendación de lavado: " . $e->getMessage());
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al obtener el detalle de la recomendación de lavado."));
        }
    }
}