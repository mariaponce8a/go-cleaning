<?php
require_once('../config/conexion.php');

class Clase_Servicios
{
    public function todos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "SELECT * FROM tb_servicios";
            $resultado = mysqli_query($conexion, $consulta);
            
            if ($resultado === false) {
                throw new Exception(mysqli_error($conexion));
            }
            
            $servicios = array();
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $servicios[] = $fila;
            }
            
            return $servicios;
        } catch (Exception $e) {
            error_log("Error en la consulta todos(): " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertar($descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "INSERT INTO tb_servicios (descripcion_servicio, costo_unitario, validar_pesaje) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("sdi", $descripcion_servicio, $costo_unitario, $validar_pesaje);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar servicio: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizar($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "UPDATE tb_servicios SET descripcion_servicio=?, costo_unitario=?, validar_pesaje=? WHERE id_servicio=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("sdii", $descripcion_servicio, $costo_unitario, $validar_pesaje, $id_servicio);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar servicio: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminar($id_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "DELETE FROM tb_servicios WHERE id_servicio=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("i", $id_servicio);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar servicio: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_servicios WHERE id_servicio=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_servicio);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $servicio = $resultado->fetch_assoc();
                    return $servicio;
                } else {
                    throw new Exception("No se encontró el servicio.");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar servicio por ID: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorDescripcion($descripcion_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_servicios WHERE descripcion_servicio LIKE ?";
            $stmt = $conexion->prepare($consulta);
            $descripcionBusqueda = "%" . $descripcion_servicio . "%";
            $stmt->bind_param("s", $descripcionBusqueda);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $servicios = array();
                while ($fila = $resultado->fetch_assoc()) {
                    $servicios[] = $fila;
                }
                return $servicios;
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar servicios por descripción: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
?>
