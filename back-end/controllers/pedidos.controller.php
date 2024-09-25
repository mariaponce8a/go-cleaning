<?php
require_once('./back-end/models/pedidos.models.php');

class Pedidos_controller
{

    public function getAllPedidos()
    {
        error_log("--------------");
        $model = new pedidos_model();
        $resultado = $model->getAllPedidos();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los pedidos"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedidos cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function getAllPedidosnoCancelados()
    {
        error_log("--------------");
        $model = new pedidos_model();
        $resultado = $model->getAllPedidosNoCancelados();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los pedidos"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedidos cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function getPedidosXId($id)
    {
        error_log("-------------- $id");
        $model = new pedidos_model();
        $resultado = $model->getPedidoXId($id);
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . json_encode($resultado));
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar"));
        } else {
            return json_encode(array("respuesta" => "1", "data" => json_decode($resultado)));
        }
    }



    public function insertarPedidoCompleto(
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
        error_log("--------------");
        $model = new pedidos_model();

        if (
            $fecha_pedido == null ||
            $fk_id_usuario == null ||
            $cantidad_articulos == null ||
            $fk_id_cliente == null ||
            $pedido_subtotal == null ||
            $estado_pago == null ||
            $fecha_entrega_estimada == null ||
            $hora_entrega_estimada == null ||
            $direccion_entrega == null ||
            $tipo_entrega == null ||
            $total == null
        ) {
            return json_encode(value: array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->registrarPedidoCompleto(
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

        error_log("RESPUESTA CONTROLLER PEDIDO --------------------" . json_encode($resultado));

        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el pedido"));
        } else {
            return json_encode(array("respuesta" => $resultado['respuesta'], "pedido" => $resultado['pedido'], "mensaje" =>  $resultado['mensaje']));
        }
    }

    public function insertPedidos(
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
        error_log("--------------");
        $model = new pedidos_model();
        if (
            $fk_id_usuario == null ||
            $cantidad_articulos == null ||
            $fk_id_cliente == null ||
            $fk_id_descuentos == null ||
            $pedido_subtotal == null ||
            $estado_pago == null ||
            $valor_pago == null ||
            $direccion_recoleccion == null ||
            $fecha_entrega_estimada == null ||
            $hora_entrega_estimada == null ||
            $direccion_entrega == null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->registrarPedido(
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
        );

        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el pedido"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido registrado con éxito"));
        }
    }


    public function insertItemsPedidos(
        $fk_id_servicio,
        $libras,
        $precio_servicio,
        $fk_id_pedido,
        $descripcion_articulo
    ) {
        error_log("-------------- BODY DE PEDIDOS DETALLE: " . $fk_id_servicio . " - " . $libras . " - " . $precio_servicio . " - " . $fk_id_pedido . " - " . $descripcion_articulo);
        $model = new pedidos_model();
        if (
            $fk_id_servicio == null ||
            $libras == null ||
            $precio_servicio == null ||
            $fk_id_pedido == null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->agregarItemsAPedido(
            $fk_id_servicio,
            $libras,
            $precio_servicio,
            $fk_id_pedido,
            $descripcion_articulo
        );

        error_log("----------RESULTADO INSERT items DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el item pedido" . $descripcion_articulo));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido registrado con éxito"));
        }
    }


    public function ejecutarFacturacion(
        $id_pedido_cabecera,
        $estado_facturacion
    ) {
        error_log("-------------- BODY DE PEDIDOS DETALLE: " . $id_pedido_cabecera . " - " . $estado_facturacion);
        $model = new pedidos_model();
        if (
            $id_pedido_cabecera == null ||
            $estado_facturacion == null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->ejecutarFacturacion(
            $id_pedido_cabecera,
            $estado_facturacion
        );

        error_log("----------RESULTADO INSERT items DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para ejecutar la facturación"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido registrado con éxito"));
        }
    }




    public function actualizarItemsPedidos(
        $id_pedido_detalle,
        $fk_id_servicio,
        $libras,
        $precio_servicio,
        $fk_id_pedido,
        $descripcion_articulo
    ) {
        error_log("-------------- BODY DE PEDIDOS DETALLE: " . $fk_id_servicio . " - " . $libras . " - " . $precio_servicio . " - " . $fk_id_pedido . " - " . $descripcion_articulo);
        $model = new pedidos_model();
        if (
            $id_pedido_detalle == null ||
            $fk_id_servicio == null ||
            $libras == null ||
            $precio_servicio == null ||
            $fk_id_pedido == null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->editarItemsAPedido(
            $id_pedido_detalle,
            $fk_id_servicio,
            $libras,
            $precio_servicio,
            $fk_id_pedido,
            $descripcion_articulo
        );

        error_log("----------RESULTADO INSERT items DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el item pedido" . $descripcion_articulo));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido registrado con éxito"));
        }
    }


    public function updatePedidos(
        $id_pedido_cabecera,
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
    ) {
        error_log("--------------");
        $model = new pedidos_model();
        if (
            $id_pedido_cabecera == null ||
            $fk_id_usuario == null ||
            $cantidad_articulos == null ||
            $fk_id_cliente == null ||
            $fk_id_descuentos == null ||
            $pedido_subtotal == null ||
            $estado_pago == null ||
            $valor_pago == null ||
            $fecha_hora_recoleccion_estimada == null ||
            $direccion_recoleccion == null ||
            $fecha_hora_entrega_estimada == null ||
            $direccion_entrega == null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $model->actualizarPedido(
            $id_pedido_cabecera,
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

        error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para actualizar el pedido"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido actualizado con éxito"));
        }
    }

    public function deletePedido($id_pedido_cabecera)
    {
        error_log("--------------");
        $model = new pedidos_model();
        if (
            $id_pedido_cabecera === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Pedido no seleccionado"));
        }
        $resultado = $model->eliminarPedido($id_pedido_cabecera);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el pedido"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido eliminado con éxito"));
        }
    }

    public function getOrdenPedidos($id_pedido_cabecera)
    {
        error_log("--------------");
        $pedidoModel = new pedidos_model();
        $resultado = $pedidoModel->OrdenPedido($id_pedido_cabecera);
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado === false || empty($resultado)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas al cargar el pedido"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function getPedidosNoFinalizados()
    {
        error_log("--------------");
        $model = new pedidos_model();
        $resultado = $model->getPedidosNoFinalizados();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los pedidos"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedidos no finalizados cargados con éxito", "data" => json_decode($resultado)));
        }
    }
}
