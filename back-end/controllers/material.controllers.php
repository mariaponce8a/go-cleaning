<?php
require_once('./back-end/models/material.models.php');

class Materiales_controller
{
    public function getAllMaterials()
    {
        error_log("--------------");
        $materialModel = new Clase_Material();
        $resultado = $materialModel->todos();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . json_encode($resultado));
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los materiales"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Materiales cargados con éxito", "data" => $resultado));
        }
    }

public function insertMaterial($descripcion_material, $imagen)
{
    error_log("-------------- Insert Material Method Called ----------------");
    
    $materialModel = new Clase_Material();
    
    // Validación de campos
    if (empty($descripcion_material)) {
        return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete la descripción del material."));
    }

    // Verificación de imagen en Base64
    if (empty($imagen)) {
        return json_encode(array("respuesta" => "0", "mensaje" => "La imagen es requerida."));
    }

    // Llama al modelo para insertar el material
    $resultado = $materialModel->insertar($descripcion_material, $imagen);
    
    error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
    
    if ($resultado === false) {
        return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el material"));
    } else {
        return json_encode(array("respuesta" => "1", "mensaje" => "Material registrado con éxito"));
    }
}

    
public function updateMaterial($id_material, $descripcion_material, $imagen = null)
{
    error_log("-------------- Update Material Method Called ----------------");

    $materialModel = new Clase_Material();

    if (empty($id_material) || empty($descripcion_material)) {
        return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
    }
    if ($imagen !== null && empty($imagen)) {
        return json_encode(array("respuesta" => "0", "mensaje" => "La imagen es inválida o está vacía."));
    }
    $resultado = $materialModel->actualizar($id_material, $descripcion_material, $imagen);
    
    error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
    
    if ($resultado === false) {
        return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el material"));
    } else {
        return json_encode(array("respuesta" => "1", "mensaje" => "Material actualizado con éxito"));
    }
}


    public function deleteMaterial($id_material)
    {
        error_log("--------------");
        $materialModel = new Clase_Material();
        if ($id_material === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Material no seleccionado"));
        }
        $resultado = $materialModel->eliminar($id_material);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el material"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Material eliminado con éxito"));
        }
    }

    public function getMaterialDetail($id_material)
    {
        error_log("--------------");
        $materialModel = new Clase_Material();
        if ($id_material === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el parámetro ID para obtener el detalle del material."));
        }
        try {
            $materialDetalle = $materialModel->buscarPorId($id_material);
            error_log("----------RESULTADO DETALLE DESDE CONTROLLER: " . json_encode($materialDetalle));
            if ($materialDetalle == false) {
                return json_encode(array("respuesta" => "0", "mensaje" => "No se encontró el material."));
            } else {
                return json_encode(array("respuesta" => "1", "mensaje" => "Detalle del material cargado con éxito", "data" => $materialDetalle));
            }
        } catch (Exception $e) {
            error_log("Error al obtener el detalle del material: " . $e->getMessage());
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al obtener el detalle del material."));
        }
    }
}