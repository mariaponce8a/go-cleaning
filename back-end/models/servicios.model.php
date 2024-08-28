<?php
require_once('./back-end/config/conexion.php');

class Clase_Servicios
{
    public function getAllServices()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "select * from tb_servicios";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar los servicios");
            } else {
                $services = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $services[] = $fila;
                }
                return json_encode($services);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertService($descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "INSERT INTO tb_servicios (descripcion_servicio, costo_unitario, validar_pesaje) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sdi", $descripcion_servicio, $costo_unitario, $validar_pesaje);

            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Servicio insertado con éxito"));
            } else {
                throw new Exception("Problemas al insertar el servicio");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function updateService($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "UPDATE tb_servicios SET descripcion_servicio = ?, costo_unitario = ?, validar_pesaje = ? WHERE id_servicio = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sdii", $descripcion_servicio, $costo_unitario, $validar_pesaje, $id_servicio);

            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Servicio actualizado con éxito"));
            } else {
                throw new Exception("Problemas al actualizar el servicio");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function deleteService($id_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "DELETE FROM tb_servicios WHERE id_servicio = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_servicio);

            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Servicio eliminado con éxito"));
            } else {
                throw new Exception("Problemas al eliminar el servicio");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function findServiceById($id_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_servicios WHERE id_servicio = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_servicio);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $service = $resultado->fetch_assoc();
                    return json_encode($service);
                } else {
                    throw new Exception("No se encontró el servicio.");
                }
            } else {
                throw new Exception("Problemas al buscar el servicio por ID");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function findServiceByDescription($descripcion_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_servicios WHERE descripcion_servicio LIKE ?";
            $stmt = $conexion->prepare($query);
            $descripcionBusqueda = "%" . $descripcion_servicio . "%";
            $stmt->bind_param("s", $descripcionBusqueda);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $services = array();
                while ($fila = $resultado->fetch_assoc()) {
                    $services[] = $fila;
                }
                return json_encode($services);
            } else {
                throw new Exception("Problemas al buscar los servicios por descripción");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
?>
