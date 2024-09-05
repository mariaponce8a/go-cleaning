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


    public function insertPedidos(
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
    ) {
        error_log("--------------");
        $model = new pedidos_model();
        if (
            $fecha_pedido == null ||
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
        $resultado = $model->registrarPedido(
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

        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el pedido"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Pedido registrado con éxito"));
        }
    }


    // public function updateUser($id, $nombre, $apellido, $perfil, $usuario, $clave) {}

    // public function deleteUsuario($id) {}
}
