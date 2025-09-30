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
            p.id_pedido_cabecera, p.fecha_pedido, p.fk_id_usuario, p.estado_facturacion, p.total , p.estado_pedido ,
            u.usuario , p.cantidad_articulos,
            p.fk_id_cliente, c.identificacion_cliente, c.correo_cliente , c.nombre_cliente, c.apellido_cliente,
            p.fk_id_descuentos, d.tipo_descuento_desc , d.cantidad_descuento , p.pedido_subtotal, p.estado_pago, p.valor_pago,
            p.fecha_recoleccion_estimada, p.direccion_recoleccion, p.fecha_entrega_estimada,
            p.direccion_entrega, p.tipo_entrega,  e.descripcion_estado
            from tb_pedido p
            left join tb_usuarios_plataforma u on u.id_usuario = p.fk_id_usuario
            left join tb_clientes_registrados c on c.id_cliente = p.fk_id_cliente
            left join tb_asignaciones_empleado a ON p.id_pedido_cabecera = a.fk_id_pedido
            left join tb_estados e ON a.fk_id_estado = e.id_estado
            left join tb_tipo_descuentos d on d.id_tipo_descuento = p.fk_id_descuentos 
            order by p.id_pedido_cabecera desc"; 
          
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

    public function getAllPedidosNoCancelados()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "select p.id_pedido_cabecera, p.fecha_pedido, p.fk_id_usuario, p.estado_facturacion, 
            p.total, p.estado_pedido, p.cantidad_articulos, p.fk_id_cliente, c.identificacion_cliente, 
            c.correo_cliente, c.nombre_cliente, c.apellido_cliente, p.fk_id_descuentos, 
            p.pedido_subtotal, p.estado_pago, p.valor_pago,p.fecha_recoleccion_estimada, 
            p.direccion_recoleccion, p.fecha_entrega_estimada, p.direccion_entrega, 
            p.tipo_entrega FROM tb_pedido p 
            INNER JOIN tb_clientes_registrados c ON c.id_cliente = p.fk_id_cliente 
            WHERE p.estado_pedido = 1";
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

    public function getPedidoXId($idpedido)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            // Consultar la cabecera del pedido
            $query = "
           select
            p.id_pedido_cabecera, p.fecha_pedido, p.fk_id_usuario, p.estado_facturacion , p.cantidad_articulos,
            p.fk_id_cliente, c.identificacion_cliente, c.correo_cliente , c.nombre_cliente, c.apellido_cliente,
            p.fk_id_descuentos, p.pedido_subtotal, p.estado_pago, p.valor_pago,  p.total , p.estado_pedido ,
            p.fecha_recoleccion_estimada, p.direccion_recoleccion, p.fecha_entrega_estimada,
            p.direccion_entrega, p.tipo_entrega
            from tb_pedido p inner join tb_clientes_registrados c on c.id_cliente = p.fk_id_cliente
            where p.id_pedido_cabecera = ?
            ";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('i', $idpedido);

            if (!$stmt->execute()) {
                throw new Exception("Problemas al cargar los datos de la cabecera del pedido");
            }

            $cabecera = array();
            $resultado = $stmt->get_result();
            while ($fila = $resultado->fetch_assoc()) {
                $cabecera[] = $fila;
            }

            // Consultar el detalle del pedido
            $query2 = "SELECT * FROM tb_pedido_detalle WHERE fk_id_pedido = ?";
            $stmt2 = $conexion->prepare($query2);
            $stmt2->bind_param('i', $idpedido);

            if (!$stmt2->execute()) {
                throw new Exception("Problemas al cargar los datos del detalle del pedido");
            }

            $detalle = array();
            $resultado2 = $stmt2->get_result();
            while ($filad = $resultado2->fetch_assoc()) {
                $detalle[] = $filad;
            }

            // Retornar el resultado en formato JSON
            return json_encode(array("pedido" => $cabecera[0], "detalle" => $detalle));
        } catch (Exception $e) {
            error_log($e->getMessage() . "----DESDE EL MODELO");
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
    public function registrarPedidoCompleto(
        $fecha_pedido,
        $fk_id_usuario,
        $cantidad_articulos,
        $fk_id_cliente,
        $fk_id_descuentos,
        $pedido_subtotal,
        $estado_pago,
        $valor_pago,
        $fecha_recoleccion_estimada,
        $hora_recoleccion_estimada,
        $direccion_recoleccion,
        $fecha_entrega_estimada,
        $hora_entrega_estimada,
        $direccion_entrega,
        $tipo_entrega,
        $total,
        $detalle
    ) {
        try {

            $detallestr = json_encode($detalle);
            error_log($detalle);
            error_log($detallestr);
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $callProcedure = "CALL InsertarPedidoConDetalle(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($callProcedure);
            $stmt->bind_param(
                "siiiidsdsssssssds",
                $fecha_pedido,
                $fk_id_usuario,
                $cantidad_articulos,
                $fk_id_cliente,
                $fk_id_descuentos,
                $pedido_subtotal,
                $estado_pago,
                $valor_pago,
                $fecha_recoleccion_estimada,
                $hora_recoleccion_estimada,
                $direccion_recoleccion,
                $fecha_entrega_estimada,
                $hora_entrega_estimada,
                $direccion_entrega,
                $tipo_entrega,
                $total,
                $detalle
            );


            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                error_log("----------------------RESPUESTA DEL PEDIDO MODELO------------" . json_encode($row));
                if ($row['respuesta'] == 1) {
                    return $row;
                } else {
                    throw new Exception(false);
                }
            } else {
                throw new Exception(false);
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
        $fk_id_usuario,
        $cantidad_articulos,
        $fk_id_cliente,
        $fk_id_descuentos,
        $pedido_subtotal,
        $estado_pago,
        $valor_pago,
        $fecha_recoleccion_estimada,
        $hora_recoleccion_estimada,
        $direccion_recoleccion,
        $fecha_entrega_estimada,
        $hora_entrega_estimada,
        $direccion_entrega,
        $tipo_entrega
    ) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "
            INSERT INTO tb_pedido
            (
            fecha_pedido, fk_id_usuario, cantidad_articulos,
            fk_id_cliente, fk_id_descuentos, pedido_subtotal,
            estado_pago, valor_pago, fecha_recoleccion_estimada,
            hora_recoleccion_estimada , direccion_recoleccion, 
            fecha_entrega_estimada, hora_entrega_estimada
            direccion_entrega, tipo_entrega
            )
              VALUES
            (
               CURRENT_TIMESTAMP,?,?,?,?,?,?,?,?,?,?,?,?,?,?
            )";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "iiiidsdsssssss",
                $fk_id_usuario,
                $cantidad_articulos,
                $fk_id_cliente,
                $fk_id_descuentos,
                $pedido_subtotal,
                $estado_pago,
                $valor_pago,
                $fecha_recoleccion_estimada,
                $hora_recoleccion_estimada,
                $direccion_recoleccion,
                $fecha_entrega_estimada,
                $hora_entrega_estimada,
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


    public function ejecutarFacturacion(
        $id_pedido_cabecera,
        $estado_facturacion
    ) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar(); 
            $query = "update  tb_pedido  SET  estado_facturacion  = ? WHERE  id_pedido_cabecera = ? ";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "ii",
                $estado_facturacion,
                $id_pedido_cabecera
            );
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                 
                error_log("???resultado de facturacion". $resultado);
                return true;
            } else {
                throw new Exception("Problemas al ejecutar la facturación");
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

    public function actualizarPedido(
        $id_pedido_cabecera, //i
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
            $conexion->begin_transaction();
            $query = "
            UPDATE tb_pedido
            SET
            fk_id_usuario = ?, cantidad_articulos = ?,
            fk_id_cliente = ?, fk_id_descuentos = ?, pedido_subtotal = ?,
            estado_pago = ?, valor_pago = ?, fecha_hora_recoleccion_estimada = ?,
            direccion_recoleccion = ?, fecha_hora_entrega_estimada = ?, direccion_entrega = ?, tipo_entrega = ? 
            where id_pedido_cabecera = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "iiiidsdsssssi",
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
                $tipo_entrega,
                $id_pedido_cabecera
            );
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $conexion->commit();
                error_log("?????????????????????RESULTADO UPDATE DESDE MODEL PEDIDOS" . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al actualizar el pedido");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $conexion->rollback();
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }


    public function eliminarPedido($id_pedido_cabecera)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $conexion->begin_transaction();

            // Borrar de la segunda tabla
            $query2 = "DELETE FROM tb_pedido_detalle WHERE fk_id_pedido = ?";
            $stmt2 = $conexion->prepare($query2);
            $stmt2->bind_param("i", $id_pedido_cabecera);
            $stmt2->execute();

            // Borrar de la primera tabla
            $query1 = "DELETE FROM tb_pedido WHERE id_pedido_cabecera = ?";
            $stmt1 = $conexion->prepare($query1);
            $stmt1->bind_param("i", $id_pedido_cabecera);
            $stmt1->execute();

            // Confirmar la transacción
            $conexion->commit();
            if (!$stmt1  || ! $stmt2) {
                throw new Exception(false);
            } else {
                return true;
            }
        } catch (Exception $e) {
            $conexion->rollback();
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }


    public function agregarItemsAPedido(
        $fk_id_servicio,
        $libras,
        $precio_servicio,
        $fk_id_pedido,
        $descripcion_articulo
    ) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "
           INSERT INTO tb_pedido_detalle (
            fk_id_servicio, 
            libras, 
            precio_servicio, 
            fk_id_pedido, 
            descripcion_articulo
            ) VALUES (
                ?,?,?,?,?
            )";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "iddis",
                $fk_id_servicio,
                $libras,
                $precio_servicio,
                $fk_id_pedido,
                $descripcion_articulo
            );

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al registrar el item de pedido");
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


    public function editarItemsAPedido(
        $id_pedido_detalle,
        $fk_id_servicio,
        $libras,
        $precio_servicio,
        $fk_id_pedido,
        $descripcion_articulo
    ) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "
           UPDATE tb_pedido_detalle SET 
            fk_id_servicio = ?, 
            libras = ?, 
            precio_servicio = ?, 
            fk_id_pedido = ?, 
            descripcion_articulo = ? 
            where id_pedido_detalle = ?
            ";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param(
                "iddisi",
                $fk_id_servicio,
                $libras,
                $precio_servicio,
                $fk_id_pedido,
                $descripcion_articulo,
                $id_pedido_detalle
            );

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Problemas al actualizar el item de pedido");
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

    public function OrdenPedido($id_pedido_cabecera)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $query = "
                    SELECT 
                    p.fecha_pedido,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario_completo,
                    c.identificacion_cliente,
                    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_cliente_completo,
                    pd.descripcion_articulo, 
                    pd.precio_servicio,
                    pd.libras,
                    d.tipo_descuento_desc,
                    p.cantidad_articulos, 
                    p.pedido_subtotal,
                    p.estado_pago,
                    p.valor_pago,
                    p.fecha_recoleccion_estimada,
                    p.hora_recoleccion_estimada,
                    p.fecha_entrega_estimada,
                    p.direccion_entrega,
                    p.tipo_entrega,
                    s.descripcion_servicio,
                    s.costo_unitario,
                    pd.id_pedido_detalle
                FROM 
                    tb_pedido p
                LEFT JOIN 
                    tb_pedido_detalle pd ON p.id_pedido_cabecera = pd.fk_id_pedido
                LEFT JOIN 
                    tb_usuarios_plataforma u ON p.fk_id_usuario = u.id_usuario
                LEFT JOIN 
                    tb_clientes_registrados c ON p.fk_id_cliente = c.id_cliente
                LEFT JOIN 
                    tb_tipo_descuentos d ON p.fk_id_descuentos = d.id_tipo_descuento
                LEFT JOIN 
                    tb_servicios s ON pd.fk_id_servicio = s.id_servicio
                WHERE 
                    p.id_pedido_cabecera = ?
            ";

            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_pedido_cabecera);

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $pedidos = array();

            while ($fila = $result->fetch_assoc()) {
                $pedidos[] = $fila;
            }

            return json_encode($pedidos);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function getPedidosNoFinalizados()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "SELECT 
                                    p.fecha_pedido,
                                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario_completo,
                                    c.identificacion_cliente,
                                    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_cliente_completo,
                                    p.cantidad_articulos, 
                                    p.estado_pago,
                                    p.valor_pago,
                                    p.fecha_recoleccion_estimada,
                                    p.direccion_entrega,
                                    p.tipo_entrega,
                                    p.fecha_entrega_estimada,
                                    e.descripcion_estado
                                FROM 
                                    tb_pedido p
                                LEFT JOIN 
                                    tb_usuarios_plataforma u ON p.fk_id_usuario = u.id_usuario
                                LEFT JOIN 
                                    tb_clientes_registrados c ON p.fk_id_cliente = c.id_cliente
                                LEFT JOIN 
                                    tb_asignaciones_empleado a ON p.id_pedido_cabecera = a.fk_id_pedido
                                LEFT JOIN 
                                    tb_estados e ON a.fk_id_estado = e.id_estado
                                ORDER BY p.id_pedido_cabecera DESC
                                ";
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

    public function GenerarFactura($id_pedido_cabecera)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();

        $query = "
            		SELECT 
                    p.fecha_pedido,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario_completo,
                    c.identificacion_cliente,
                    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_cliente_completo,
                    c.telefono_cliente,
                    c.correo_cliente,
                    pd.precio_servicio,
                    d.tipo_descuento_desc,
                    pd.cantidad, 
                    p.pedido_subtotal,
                    s.descripcion_servicio,
                    s.costo_unitario,
                    pd.id_pedido_detalle,
                    pd.libras,
                    pd.id_pedido_detalle
                FROM 
                    tb_pedido p
                LEFT JOIN 
                    tb_pedido_detalle pd ON p.id_pedido_cabecera = pd.fk_id_pedido
                LEFT JOIN 
                    tb_usuarios_plataforma u ON p.fk_id_usuario = u.id_usuario
                LEFT JOIN 
                    tb_clientes_registrados c ON p.fk_id_cliente = c.id_cliente
                LEFT JOIN 
                    tb_tipo_descuentos d ON p.fk_id_descuentos = d.id_tipo_descuento
                LEFT JOIN 
                    tb_servicios s ON pd.fk_id_servicio = s.id_servicio
                WHERE 
                    p.id_pedido_cabecera = ? and
                    p.estado_facturacion= 1 
            
        ";

        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_pedido_cabecera);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $factura = array();

        while ($fila = $result->fetch_assoc()) {
            $factura[] = $fila;
        }

        return json_encode($factura);
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
