<?php
require_once('./back-end/config/conexion.php');

class Clase_RecomendacionLavado
{
    public function todos()
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        $consulta = "SELECT r.id_recomendacion_lavado,
                            m.descripcion_material,
                            s.descripcion_servicio
                        FROM
                            tb_recomendacion_lavado r
                        JOIN
                            tb_material m ON r.fk_id_material = m.id_material
                        JOIN
                            tb_servicios s ON r.fk_id_servicio = s.id_servicio;";
        
        $stmt = $conexion->prepare($consulta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $recomendacionLavado = array();
        while ($fila = $resultado->fetch_assoc()) {
            $recomendacionLavado[] = $fila;
        }
        
        return $recomendacionLavado;
        } catch (Exception $e) {
            throw new RuntimeException("Error en la consulta listarTodos() de recomendacion_lavado: " . $e->getMessage(), 500);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conexion)) {
                $conexion->close();
            }
        }
}

    public function insertar($descripcion_material, $descripcion_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            // Obtener el id_material basado en la descripcion_material
            $consulta_material = "SELECT id_material FROM tb_material WHERE descripcion_material = ?";
            $stmt_material = $conexion->prepare($consulta_material);
            if (!$stmt_material) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt_material->bind_param("s", $descripcion_material);
            $stmt_material->execute();
            $stmt_material->bind_result($id_material);
            
            // Si el material no existe, manejar el error
            if (!$stmt_material->fetch()) {
                throw new Exception("El material '$descripcion_material' no fue encontrado.");
            }
            $stmt_material->close();
            
            // Obtener el id_servicio basado en la descripcion_servicio
            $consulta_servicio = "SELECT id_servicio FROM tb_servicios WHERE descripcion_servicio = ?";
            $stmt_servicio = $conexion->prepare($consulta_servicio);
            if (!$stmt_servicio) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt_servicio->bind_param("s", $descripcion_servicio);
            $stmt_servicio->execute();
            $stmt_servicio->bind_result($id_servicio);
            
            // Si el servicio no existe, manejar el error
            if (!$stmt_servicio->fetch()) {
                throw new Exception("El servicio '$descripcion_servicio' no fue encontrado.");
            }
            $stmt_servicio->close();
    
            // Insertar la relación con los ids obtenidos
            $consulta = "INSERT INTO tb_recomendacion_lavado (fk_id_material, fk_id_servicio) VALUES (?, ?)";
            $stmt = $conexion->prepare($consulta);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("ii", $id_material, $id_servicio);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar: " . $e->getMessage());
            return "Error al insertar: " . $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizar($id_recomendacion_lavado, $descripcion_material, $descripcion_servicio)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
         // Obtener el id_material basado en la descripcion_material
         $consulta_material = "SELECT id_material FROM tb_material WHERE descripcion_material = ?";
         $stmt_material = $conexion->prepare($consulta_material);
         if (!$stmt_material) {
             throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
         }
         $stmt_material->bind_param("s", $descripcion_material);
         $stmt_material->execute();
         $stmt_material->bind_result($id_material);
         
         // Si el material no existe, manejar el error
         if (!$stmt_material->fetch()) {
             throw new Exception("El material '$descripcion_material' no fue encontrado.");
         }
         $stmt_material->close();
         
         // Obtener el id_servicio basado en la descripcion_servicio
         $consulta_servicio = "SELECT id_servicio FROM tb_servicios WHERE descripcion_servicio = ?";
         $stmt_servicio = $conexion->prepare($consulta_servicio);
         if (!$stmt_servicio) {
             throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
         }
         $stmt_servicio->bind_param("s", $descripcion_servicio);
         $stmt_servicio->execute();
         $stmt_servicio->bind_result($id_servicio);
         
         // Si el servicio no existe, manejar el error
         if (!$stmt_servicio->fetch()) {
             throw new Exception("El servicio '$descripcion_servicio' no fue encontrado.");
         }
         $stmt_servicio->close();

        // Actualizar la relación con los ids obtenidos
        $consulta = "UPDATE tb_recomendacion_lavado SET fk_id_material = ?, fk_id_servicio = ? WHERE id_recomendacion_lavado = ?";
        $stmt = $conexion->prepare($consulta);
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt->bind_param("iii", $id_material, $id_servicio, $id_recomendacion_lavado);
        
        if ($stmt->execute()) {
            return "ok";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error al actualizar relación: " . $e->getMessage());
        return "Error al actualizar la relación: " . $e->getMessage();
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}

    public function eliminar($id_recomendacion_lavado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "DELETE FROM tb_recomendacion_lavado WHERE id_recomendacion_lavado = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_recomendacion_lavado);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conexion->close();
                return "ok"; // Devuelve "ok" cuando la eliminación es exitosa
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar: " . $e->getMessage());
            return "Error al eliminar: " . $e->getMessage();
        }
    }

    public function buscarPorId($id_recomendacion_lavado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT r.id_recomendacion,
                                m.descripcion_material,
                                s.descripcion_servicio
                            FROM
                                tb_recomendacion_lavado r
                            JOIN
                                tb_material m ON r.fk_id_material = m.id_material
                            JOIN
                                tb_servicios s ON r.fk_id_servicio = s.id_servicio;";

            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_recomendacion_lavado);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $recomendacionLavado = $resultado->fetch_assoc();
                    return $recomendacionLavado;
                } else {
                    throw new Exception("No se encontró resultados");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar por ID: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}