<?php
require_once('./back-end/models/mensajes.model.php');

class MensajesW_Controller {

    public function enviarMensajePorPedido($id_pedido_cabecera)
    {
        $mensajesModel = new MensajesW();
        if ($id_pedido_cabecera === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Falta el ID del pedido."));
        }

        $resultado = $mensajesModel->enviarMensajePorPedido($id_pedido_cabecera);
        if ($resultado === "ok") {
            return json_encode(array("respuesta" => "1", "mensaje" => "Mensaje enviado con éxito"));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => "Error al enviar el mensaje: " . $resultado));
        }
    }
}        