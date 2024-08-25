<?php
require_once('./back-end/config/conexion.php');

class Clase_Descuentos
{
    public function getAllDescuentos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_tipo_descuentos";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult === false) {
                throw new Exception("Problemas al cargar los descuentos");
            } else {
                $descuentos = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $descuentos[] = $fila;
                }

                return json_encode($descuentos);
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

    public function registrarDescuento($tipo_descuento_desc, $cantidad_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            // Verificar que cantidad_descuento sea un número y esté en el rango permitido
            if (!is_numeric($cantidad_descuento) || $cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
                throw new Exception("El valor de cantidad_descuento debe ser un número entre -9.99 y 9.99.");
            }

            $query = "INSERT INTO tb_tipo_descuentos (tipo_descuento_desc, cantidad_descuento) VALUES (?, ?)";
            $stmt = $conexion->prepare($query);
            $cantidad_descuento = floatval($cantidad_descuento);
            $stmt->bind_param("sd", $tipo_descuento_desc, $cantidad_descuento);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al registrar el descuento");
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

    public function actualizarDescuento($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            // Verificar que cantidad_descuento sea un número y esté en el rango permitido
            if (!is_numeric($cantidad_descuento) || $cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
                throw new Exception("El valor de cantidad_descuento debe ser un número entre -9.99 y 9.99.");
            }

            $query = "UPDATE tb_tipo_descuentos SET tipo_descuento_desc=?, cantidad_descuento=? WHERE id_tipo_descuento=?";
            $stmt = $conexion->prepare($query);
            $cantidad_descuento = floatval($cantidad_descuento);
            $stmt->bind_param("sdi", $tipo_descuento_desc, $cantidad_descuento, $id_tipo_descuento);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al actualizar el descuento");
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

    public function eliminarDescuento($id_tipo_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "DELETE FROM tb_tipo_descuentos WHERE id_tipo_descuento = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_tipo_descuento);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al eliminar el descuento");
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

    public function getDescuentoById($id_tipo_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_tipo_descuentos WHERE id_tipo_descuento = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_tipo_descuento);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $descuento = $resultado->fetch_assoc();
                    return json_encode($descuento);
                } else {
                    throw new Exception("No se encontró el descuento.");
                }
            } else {
                throw new Exception("Problemas al buscar el descuento por ID");
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

    public function buscarDescuentoByNombre($tipo_descuento_desc)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT * FROM tb_tipo_descuentos WHERE tipo_descuento_desc LIKE ?";
            $stmt = $conexion->prepare($query);

            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }

            $nombreBusqueda = "%" . $tipo_descuento_desc . "%";
            $stmt->bind_param("s", $nombreBusqueda);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $descuentos = array();
                while ($fila = $resultado->fetch_assoc()) {
                    $descuentos[] = $fila;
                }
                return json_encode($descuentos);
            } else {
                throw new Exception("Problemas al buscar descuentos por nombre");
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
?>
