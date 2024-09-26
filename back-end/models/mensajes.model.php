<?php
require_once('./back-end/config/conexion.php');

class MensajesW
{
    private $whatsappToken = 'EAB4qTXjtSjMBOxon1XvVAJUoqdk6cdpCqZC28Rz6jz6ASff2QET72h1geCM1AZBBN85CINgYXXjuIB8MkhcUqY55dZB25a8DzNjzwhS5pOZCz2euxI6dFX6LMbw3OpO3Re3QxwRyYapZCCz780mZCH4arWPQPJiy3zaYQvogc2sMxwlGom7GrnBWlMSBCnOXwOKg327661UaOj4wWMZApD9S6PVZAvJyyrXubDgZD';
    private $whatsappApiUrl = 'https://graph.facebook.com/v20.0/430795600120167/messages';

    private function enviarMensajeWhatsAppAPI($telefono) {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $telefono,
            'type' => 'template',
            'template' => [
                'name' => 'hello_world', // Nombre de tu plantilla
                'language' => [
                    'code' => 'en_US', // Código del idioma
                ],
            ],
        ];

        $header = [
            "Authorization: Bearer " . $this->whatsappToken,
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->whatsappApiUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }

    public function enviarMensajePorPedido($id_pedido_cabecera) {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $stmt = $conexion->prepare("
                SELECT 
                    c.telefono_cliente, e.descripcion_estado
                FROM 
                    tb_pedido p
                INNER JOIN 
                    tb_clientes_registrados c ON p.fk_id_cliente = c.id_cliente
                INNER JOIN 
                    tb_asignaciones_empleado a ON p.id_pedido_cabecera = a.fk_id_pedido
                INNER JOIN 
                    tb_estados e ON a.fk_id_estado = e.id_estado
                WHERE 
                    p.id_pedido_cabecera = ?
            ");

            $stmt->bind_param('i', $id_pedido_cabecera);
            $stmt->execute();
            $stmt->bind_result($telefono_cliente, $descripcion_estado);
            $stmt->fetch();

            if ($descripcion_estado === 'Finalizado') {
                $response = $this->enviarMensajeWhatsAppAPI($telefono_cliente);

                // Manejo de la respuesta de la API
                if (isset($response['messages']) && count($response['messages']) > 0) {
                    return "Mensaje enviado exitosamente.";
                } elseif (isset($response['error'])) {
                    return "Error al enviar el mensaje: " . $response['error']['message'];
                } else {
                    return "Mensaje enviado exitosamente, pero sin información adicional.";
                }
            } else {
                return "El pedido no está en estado completado.";
            }
        } catch (Exception $e) {
            error_log("Error al enviar mensaje por pedido: " . $e->getMessage());
            return "Error interno al procesar la solicitud.";
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
