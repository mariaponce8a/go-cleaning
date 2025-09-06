<?php
require_once('./back-end/config/conexion.php');

class Clase_AsignacionesEmpleado
{
    public function todos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT
                        a.id_asignaciones,
                        up.usuario AS usuario,
                        a.fecha_hora_inicio_asignacion AS fecha_inicio,
                        a.fecha_hora_fin_asignacion AS fecha_fin,
                        a.fk_id_pedido AS id_pedido_cabecera,
                        c.identificacion_cliente AS identificacion_cliente,
                        c.nombre_cliente AS nombre_cliente,
                        c.apellido_cliente AS apellido_cliente, 
                        s.descripcion_servicio AS descripcion_servicio,
                        pc.cantidad_articulos AS cantidad_articulos,
                        pd.descripcion_articulo AS descripcion_articulo,
                        pd.libras AS libras,
                        e.descripcion_estado AS descripcion_estado
                    FROM
                        tb_asignaciones_empleado a
                        JOIN tb_usuarios_plataforma up ON a.fk_id_usuario = up.id_usuario
                        JOIN tb_estados e ON a.fk_id_estado = e.id_estado
                        JOIN tb_pedido pc ON a.fk_id_pedido = pc.id_pedido_cabecera
                        JOIN tb_clientes_registrados c ON pc.fk_id_cliente = c.id_cliente
                        JOIN tb_pedido_detalle pd ON pc.id_pedido_cabecera = pd.fk_id_pedido
                        JOIN tb_servicios s ON pd.fk_id_servicio = s.id_servicio";;
            
            $stmt = $conexion->prepare($consulta);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $asignaciones = array();
            while ($fila = $resultado->fetch_assoc()) {
                $asignaciones[] = $fila;
            }

            return $asignaciones;
        } catch (Exception $e) {
            throw new RuntimeException("Error en la consulta todos() de asignaciones: " . $e->getMessage(), 500);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function insertar($usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            // Obtener el id_usuario basado en el nombre de usuario
            $consulta_usuario = "SELECT id_usuario FROM tb_usuarios_plataforma WHERE usuario = ?";
            $stmt_usuario = $conexion->prepare($consulta_usuario);
            $stmt_usuario->bind_param("s", $usuario);
            $stmt_usuario->execute();
            $stmt_usuario->bind_result($id_usuario);

            if (!$stmt_usuario->fetch()) {
                throw new Exception("El usuario '$usuario' no fue encontrado.");
            }
            $stmt_usuario->close();

            // Obtener el id_estado basado en la descripcion_estado
            $consulta_estado = "SELECT id_estado FROM tb_estados WHERE descripcion_estado = ?";
            $stmt_estado = $conexion->prepare($consulta_estado);
            $stmt_estado->bind_param("s", $descripcion_estado);
            $stmt_estado->execute();
            $stmt_estado->bind_result($id_estado);

            if (!$stmt_estado->fetch()) {
                throw new Exception("El estado '$descripcion_estado' no fue encontrado.");
            }
            $stmt_estado->close();

            // Insertar la nueva asignación
            $consulta = "INSERT INTO tb_asignaciones_empleado (fk_id_usuario, fecha_hora_inicio_asignacion, fecha_hora_fin_asignacion, fk_id_pedido, fk_id_estado) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("isssi", $id_usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $id_estado);

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

    public function actualizar($id_asignaciones, $usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            // Obtener el id_usuario basado en el nombre de usuario
            $consulta_usuario = "SELECT id_usuario FROM tb_usuarios_plataforma WHERE usuario = ?";
            $stmt_usuario = $conexion->prepare($consulta_usuario);
            $stmt_usuario->bind_param("s", $usuario);
            $stmt_usuario->execute();
            $stmt_usuario->bind_result($id_usuario);

            if (!$stmt_usuario->fetch()) {
                throw new Exception("El usuario '$usuario' no fue encontrado.");
            }
            $stmt_usuario->close();

            // Obtener el id_estado basado en la descripcion_estado
            $consulta_estado = "SELECT id_estado FROM tb_estados WHERE descripcion_estado = ?";
            $stmt_estado = $conexion->prepare($consulta_estado);
            $stmt_estado->bind_param("s", $descripcion_estado);
            $stmt_estado->execute();
            $stmt_estado->bind_result($id_estado);

            if (!$stmt_estado->fetch()) {
                throw new Exception("El estado '$descripcion_estado' no fue encontrado.");
            }
            $stmt_estado->close();

            // Actualizar la asignación
            $consulta = "UPDATE tb_asignaciones_empleado SET fk_id_usuario = ?, fecha_hora_inicio_asignacion = ?, fecha_hora_fin_asignacion = ?, fk_id_pedido = ?, fk_id_estado = ? WHERE id_asignaciones = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("isssii", $id_usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $id_estado, $id_asignaciones);

            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar: " . $e->getMessage());
            return "Error al actualizar: " . $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function eliminar($id_asignaciones)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "DELETE FROM tb_asignaciones_empleado WHERE id_asignaciones = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_asignaciones);

            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar: " . $e->getMessage());
            return "Error al eliminar: " . $e->getMessage();
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_asignaciones)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT a.id_asignaciones,
                                up.usuario AS nombre_usuario,
                                a.fecha_hora_inicio_asignacion,
                                a.fecha_hora_fin_asignacion,
                                a.fk_id_pedido,
                                e.descripcion_estado
                        FROM
                            tb_asignaciones_empleado a
                        JOIN
                            tb_usuarios_plataforma up ON a.fk_id_usuario = up.id_usuario
                        JOIN
                            tb_estados e ON a.fk_id_estado = e.id_estado
                        WHERE a.id_asignaciones = ?";
            
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_asignaciones);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    return $resultado->fetch_assoc();
                } else {
                    throw new Exception("No se encontró la asignación con ID '$id_asignaciones'.");
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

