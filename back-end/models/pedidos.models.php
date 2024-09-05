<?php
require_once('./back-end/config/conexion.php');


class pedidos_model
{

    public function getAllPedidos()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "select
            p.id_pedido_cabecera, p.fecha_pedido, p.fk_id_usuario,
            u.usuario , p.cantidad_articulos,
            p.fk_id_cliente, c.identificacion_cliente, c.correo_cliente , c.nombre_cliente, c.apellido_cliente,
            p.fk_id_descuentos, d.tipo_descuento_desc , d.cantidad_descuento , p.pedido_subtotal, p.estado_pago, p.valor_pago,
            p.fecha_hora_recoleccion_estimada, p.direccion_recoleccion, p.fecha_hora_entrega_estimada,
            p.direccion_entrega, p.tipo_entrega
            from tb_pedido p
            inner join tb_usuarios_plataforma u on u.id_usuario = p.fk_id_usuario
            inner join tb_clientes_registrados c on c.id_cliente = p.fk_id_cliente
            inner join tb_tipo_descuentos d on d.id_tipo_descuento = p.fk_id_descuentos";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar el pedido");
            } else {
                $data = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $data[] = $fila;
                }

                return json_encode($data);
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


    public function registrarPedido(
        $fecha_pedido,
        $fk_id_usuario, //i
        $cantidad_articulos, //i
        $fk_id_cliente, //i
        $fk_id_descuentos, //i
        $pedido_subtotal, //f
        $estado_pago,  //s
        $valor_pago, //f
        $fecha_hora_recoleccion_estimada, //s
        $direccion_recoleccion, //s
        $fecha_hora_entrega_estimada, //s
        $direccion_entrega, //s
        $tipo_entrega //s
    ) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "
            INSERT INTO tb_pedido
            (
            fecha_pedido, fk_id_usuario, cantidad_articulos,
            fk_id_cliente, fk_id_descuentos, pedido_subtotal,
            estado_pago, valor_pago, fecha_hora_recoleccion_estimada,
            direccion_recoleccion, fecha_hora_entrega_estimada, direccion_entrega, tipo_entrega
            )
              VALUES
            (
                ?, -- Para la fecha actual del pedido
                ?, -- ID del usuario (valor ficticio)
                ?, -- Cantidad de artículos (valor ficticio)
                ?, -- ID del cliente (valor ficticio)
                ?, -- ID del descuento (valor ficticio)
                ?, -- Subtotal del pedido
                ?, -- Estado del pago F , P o C (PAGO AL FINALIZAR, PAGO PARCIAL y PAGO COMPLETO)
                ?, -- Valor del pago
                ?, -- Fecha y hora de recolección estimada
                ?, -- Dirección de recolección
                ?, -- Fecha y hora de entrega estimada
                ?, -- Dirección de entrega
                ?  -- Tipo de entrega  D o L (DOMICILIO o LOCAL)
            )";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "siiiifsfsssss",
                $fecha_pedido,
                $fk_id_usuario,
                $cantidad_articulos,
                $fk_id_cliente,
                $fk_id_descuentos,
                $pedido_subtotal,
                $estado_pago,
                $valor_pago,
                $fecha_hora_recoleccion_estimada,
                $direccion_recoleccion,
                $fecha_hora_entrega_estimada,
                $direccion_entrega,
                $tipo_entrega
            );

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL PEDIDOS" . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al registrar el pedido");
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

    public function actualizarUsuario($id, $nombre, $apellido, $perfil, $usuario, $clave)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $clave_cifrada_ingresada = hash('sha256', $clave);
            $query = "update tb_usuarios_plataforma set usuario = ?, nombre =?, apellido=?, perfil=? , clave=? WHERE id_usuario= ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ssssss", $usuario, $nombre, $apellido, $perfil, $clave_cifrada_ingresada, $id);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al actualizar el usuario");
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


    public function eliminarUsuario($id)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "delete from tb_usuarios_plataforma where id_usuario = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al eliminar el usuario");
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
