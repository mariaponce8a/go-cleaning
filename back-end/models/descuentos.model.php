<?php
require_once('./back-end/config/conexion.php');

class Clase_Descuentos
{
    public function todos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }
            
            $consulta = "SELECT * FROM tb_tipo_descuentos";
            $stmt = $conexion->prepare($consulta);
            
            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }
            
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $descuentos = array();
                while ($fila = $resultado->fetch_assoc()) {
                    $descuentos[] = $fila;
                }
                return json_encode($descuentos);
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error en la consulta todos(): " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertar($tipo_descuento_desc, $cantidad_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }

            // Verificar que cantidad_descuento sea un número y esté en el rango permitido
            if (!is_numeric($cantidad_descuento) || $cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
                throw new Exception("El valor de cantidad_descuento debe ser un número entre -9.99 y 9.99.");
            }

            $consulta = "INSERT INTO tb_tipo_descuentos (tipo_descuento_desc, cantidad_descuento) VALUES (?, ?)";
            $stmt = $conexion->prepare($consulta);
            
            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }

            $cantidad_descuento = floatval($cantidad_descuento);
            $stmt->bind_param("sd", $tipo_descuento_desc, $cantidad_descuento);

            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Descuento insertado con éxito"));
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al insertar descuento: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function actualizar($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }

            // Verificar que cantidad_descuento sea un número y esté en el rango permitido
            if (!is_numeric($cantidad_descuento) || $cantidad_descuento < -9.99 || $cantidad_descuento > 9.99) {
                throw new Exception("El valor de cantidad_descuento debe ser un número entre -9.99 y 9.99.");
            }

            $consulta = "UPDATE tb_tipo_descuentos SET tipo_descuento_desc=?, cantidad_descuento=? WHERE id_tipo_descuento=?";
            $stmt = $conexion->prepare($consulta);
            
            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }

            $cantidad_descuento = floatval($cantidad_descuento);
            $stmt->bind_param("sdi", $tipo_descuento_desc, $cantidad_descuento, $id_tipo_descuento);
            
            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Descuento actualizado con éxito"));
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar descuento: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminar($id_tipo_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }

            $consulta = "DELETE FROM tb_tipo_descuentos WHERE id_tipo_descuento=?";
            $stmt = $conexion->prepare($consulta);
            
            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }

            $stmt->bind_param("i", $id_tipo_descuento);
            
            if ($stmt->execute()) {
                return json_encode(array("mensaje" => "Descuento eliminado con éxito"));
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar descuento: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_tipo_descuento)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }

            $consulta = "SELECT * FROM tb_tipo_descuentos WHERE id_tipo_descuento=?";
            $stmt = $conexion->prepare($consulta);
            
            if ($stmt === false) {
                throw new Exception("Error en prepare: " . $conexion->error);
            }

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
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar descuento por ID: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorNombre($tipo_descuento_desc)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            if ($conexion === false) {
                throw new Exception("Error en la conexión a la base de datos");
            }

            $consulta = "SELECT * FROM tb_tipo_descuentos WHERE tipo_descuento_desc LIKE ?";
            $stmt = $conexion->prepare($consulta);
            
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
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar descuentos por nombre: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
?>
