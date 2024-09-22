<?php
require_once('./back-end/config/conexion.php');

class MensajesW
{
    private $whatsappToken = 'EAB4qTXjtSjMBO8oNOu2Q3EOlMCdCLFVr17PcmNAUlZAp3vWXF0p05ZBmgIxh099Ye4E5agDe0ZBKykxnbiYJ6pZCdHZB0ZAG5Qqb9W59RBNszrknzLXZCwLjPMVG0khzZBdz1v9wZBR4VEKWlMWyo0l0YpmqUW8mIZBSHjiDcIpE835ZCLhdisH80YdPz2FLbjq0yLtnTF6tnhD0AJ15ioyUNOrjyA58GMrfeMutmsZD';
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
