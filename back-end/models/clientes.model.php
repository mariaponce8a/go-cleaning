<?php
require_once('./back-end/config/conexion.php');

class Clase_Clientes
{
    // Método para obtener todos los clientes
    public function todos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_clientes_registrados";
            $resultado = $conexion->query($consulta);

            if ($resultado === false) {
                throw new Exception($conexion->error);
            }

            $clientes = array();
            while ($fila = $resultado->fetch_assoc()) {
                $clientes[] = $fila;
            }

            return json_encode($clientes);
        } catch (Exception $e) {
            error_log("Error en obtener todos los clientes: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Método para insertar un nuevo cliente
    public function insertar($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "INSERT INTO tb_clientes_registrados (identificacion_cliente, tipo_identificacion_cliente, nombre_cliente, apellido_cliente, telefono_cliente, correo_cliente) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);

            $stmt->bind_param("ssssss", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);

            if ($stmt->execute()) {
                return json_encode(array("status" => "ok"));
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar cliente: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Método para actualizar un cliente
    public function actualizar($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "UPDATE tb_clientes_registrados SET identificacion_cliente=?, tipo_identificacion_cliente=?, nombre_cliente=?, apellido_cliente=?, telefono_cliente=?, correo_cliente=? WHERE id_cliente=?";
            $stmt = $conexion->prepare($consulta);

            $stmt->bind_param("ssssssi", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente, $id_cliente);

            if ($stmt->execute()) {
                return json_encode(array("status" => "ok"));
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Método para eliminar un cliente
    public function eliminar($id_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "DELETE FROM tb_clientes_registrados WHERE id_cliente=?";
            $stmt = $conexion->prepare($consulta);

            $stmt->bind_param("i", $id_cliente);

            if ($stmt->execute()) {
                return json_encode(array("status" => "ok"));
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar cliente: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Método para buscar un cliente por ID
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
                    return json_encode($resultado->fetch_assoc());
                } else {
                    throw new Exception("No se encontró el cliente.");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar cliente por ID: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Método para buscar clientes por nombre
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
                return json_encode($clientes);
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar clientes por nombre: " . $e->getMessage());
            return $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
