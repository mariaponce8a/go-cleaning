<?php
require_once('./back-end/config/conexion.php');

class MensajesW
{
    private $whatsappToken = 'EAB4qTXjtSjMBPJ2phpHrZAkaHYltRE47XZAl7ZBna1tF4PXQZA0vs383c56ozK6mZAL1Ivg9WvthjOTVtQmRW3l1ddOzyybQN8ijGzE6Yy3A1tAMT3SRfXHUZCCq18JysgyjiQK7YmwYRJV800LsmnCEZAzZBzO7OnnsgNR7anLfxb5IZBcgr0JUoxLXBoIZBHZB1dnvZAgN6mMssw6kZBraTYUg1bx1cBaRhY2xJZCL6wyewGWZCjRDiYZD';
    private $whatsappApiUrl = 'https://graph.facebook.com/v22.0/430795600120167/messages';

    private function enviarMensajeWhatsAppAPI($telefono)
    {
        try {
            error_log("TELEFONO PARA MENSAJE WS   - >>>> " . $telefono);
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $telefono,  // Usa el teléfono pasado como parámetro
                'type' => 'template',
                'template' => [
                    'name' => 'hello_world',
                    'language' => [
                        'code' => 'en_US',
                    ],
                ],
            ];

            $header = [
                "Authorization: Bearer " . $this->whatsappToken,
                "Content-Type: application/json"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $this->whatsappApiUrl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $curl_response = curl_exec($ch);

            if ($curl_response === false) {
                $error_msg = curl_error($ch);
                error_log("CURL ERROR: " . $error_msg);
                curl_close($ch);
                return ['error' => ['message' => $error_msg]];
            }

            $response = json_decode($curl_response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON DECODE ERROR: " . json_last_error_msg());
                error_log("RAW RESPONSE: " . $curl_response);
                return ['error' => ['message' => 'JSON decode error: ' . json_last_error_msg()]];
            }

            curl_close($ch);
            error_log("RESPUESTA DE ENVIAR MENSAJE " . json_encode($response));
        } catch (Exception $ex) {
            error_log("EXCEPCION DE MENSAJE --" . $ex->getMessage());
            return ['error' => ['message' => $ex->getMessage()]];
        }
        error_log("RESPUESTA FINAL DE ENVIAR MENSAJE " . json_encode($response));
        return $response;
    }

    public function enviarMensajePorPedido($id_pedido_cabecera)
    {
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
                error_log("RESPUESTA WS" . json_encode($response) . "  " . $telefono_cliente);
                // Manejo de la respuesta de la API
                if (isset($response['messages']) && count($response['messages']) > 0) {
                    return "Mensaje enviado exitosamente.";
                } elseif (isset($response['error'])) {
                    error_log("error elif" . $response['error']['message']);
                    return "Error al enviar el mensaje: " . $response['error']['message'];
                } else {
                    return "Mensaje enviado exitosamente, pero sin información adicional.";
                }
            } else {
                return "El pedido no está en estado completado.";
            }
        } catch (Exception $e) {
            error_log("error execpcion");
            error_log("Error al enviar mensaje por pedido: " . $e->getMessage());
            return "Error interno al procesar la solicitud.";
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
}
