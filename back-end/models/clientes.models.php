<?php
require_once('../config/conexion.php');

class Clase_Clientes
{
    public function todos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "SELECT * FROM tb_clientes_registrados";
            $resultado = mysqli_query($conexion, $consulta);
            
            if ($resultado === false) {
                throw new Exception(mysqli_error($conexion));
            }
            
            $clientes = array();
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $clientes[] = $fila;
            }
            
            return $clientes;
        } catch (Exception $e) {
            error_log("Error en la consulta todos(): " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertar($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "INSERT INTO tb_clientes_registrados (identificacion_cliente, tipo_identificacion_cliente, nombre_cliente, apellido_cliente, telefono_cliente, correo_cliente) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("ssssss", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar cliente: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizar($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "UPDATE tb_clientes_registrados SET identificacion_cliente=?, tipo_identificacion_cliente=?, nombre_cliente=?, apellido_cliente=?, telefono_cliente=?, correo_cliente=? WHERE id_cliente=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("ssssssi", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente, $id_cliente);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminar($id_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "DELETE FROM tb_clientes_registrados WHERE id_cliente=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("i", $id_cliente);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar cliente: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_clientes_registrados WHERE id_cliente=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_cliente);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $cliente = $resultado->fetch_assoc();
                    return $cliente;
                } else {
                    throw new Exception("No se encontró el cliente.");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar cliente por ID: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorNombre($nombre_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_clientes_registrados WHERE nombre_cliente LIKE ?";
            $stmt = $conexion->prepare($consulta);
            $nombreBusqueda = "%" . $nombre_cliente . "%";
            $stmt->bind_param("s", $nombreBusqueda);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $clientes = array();
                while ($fila = $resultado->fetch_assoc()) {
                    $clientes[] = $fila;
                }
                return $clientes;
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar clientes por nombre: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
?>
