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

    public function registrarServicio ($descripcion_servicio, $costo_unitario, $validar_pesaje, $maximo_articulos)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "insert into tb_servicios (descripcion_servicio, costo_unitario, validar_pesaje, maximo_articulos) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sdii", $descripcion_servicio, $costo_unitario, $validar_pesaje, $maximo_articulos);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al insertar el servicio");
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


    public function actualizarServicios($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje, $maximo_articulos)
    {
        try { 
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "update tb_servicios SET descripcion_servicio = ?, costo_unitario = ?, validar_pesaje = ?, maximo_articulos = ? WHERE id_servicio = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sdiii", $descripcion_servicio, $costo_unitario, $validar_pesaje, $maximo_articulos, $id_servicio);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
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

    public function eliminarServicios($id_servicio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "delete from tb_servicios where id_servicio = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_servicio);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
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
