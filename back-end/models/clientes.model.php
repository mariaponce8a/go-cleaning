<?php
require_once('./back-end/config/conexion.php');

class Clase_Clientes
{
    public function getAllClientes()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_clientes_registrados";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar los clientes");
            } else {
                $clientes = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $clientes[] = $fila;
                }

                return json_encode($clientes);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function registrarCliente($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "INSERT INTO tb_clientes_registrados (identificacion_cliente, tipo_identificacion_cliente, nombre_cliente, apellido_cliente, telefono_cliente, correo_cliente) VALUES (?,?,?,?,?,?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ssssss", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al registrar el cliente");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizarCliente($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "UPDATE tb_clientes_registrados SET identificacion_cliente = ?, tipo_identificacion_cliente = ?, nombre_cliente = ?, apellido_cliente = ?, telefono_cliente = ?, correo_cliente = ? WHERE id_cliente = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ssssssi", $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente, $id_cliente);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al actualizar el cliente");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminarCliente($id_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "DELETE FROM tb_clientes_registrados WHERE id_cliente = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_cliente);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al eliminar el cliente");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarClientePorNombre($nombre_cliente)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_clientes_registrados WHERE nombre_cliente LIKE ?";
            $stmt = $conexion->prepare($query);
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
                throw new Exception("Problemas al buscar los clientes por nombre");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
