<?php
require_once('./back-end/config/conexion.php');

class Clase_Estados
{
    public function todos()
    {
        try{
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_estados"; 
            $resultado = mysqli_query($conexion, $consulta);
            
            if ($resultado === false) {
                throw new Exception(mysqli_error($conexion));
            }
            
            $estados = array();
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $estados[] = $fila;
            }
            
            return $estados;
        } catch (Exception $e) {
            error_log("Error en la consulta todos(): " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertar($descripcion_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "INSERT INTO tb_estados (descripcion_estado) VALUES (?)";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("s", $descripcion_estado);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar estado: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizar($id_estado, $descripcion_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "UPDATE tb_estados SET descripcion_estado=? WHERE id_estado=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("si", $descripcion_estado, $id_estado);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminar($id_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "DELETE FROM tb_estados WHERE id_estado=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("i", $id_estado);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar estado: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_estados WHERE id_estado=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_estado);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $estados = $resultado->fetch_assoc();
                    return $estados;
                } else {
                    throw new Exception("No se encontró el estado.");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar estado por ID: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

}