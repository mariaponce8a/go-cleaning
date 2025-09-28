<?php
require_once('./back-end/config/conexion.php');    
require_once './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $config;
    
    public function __construct()
    {
        $this->config = [
            'fromEmail' => 'nathaliaparedes.921@gmail.com',
            'fromName' => 'Sistema Go Cleaning',
            'smtpHost' => 'smtp.gmail.com',
            'smtpPort' => 587,
            'smtpUsername' => 'nathaliaparedes.921@gmail.com',
            'smtpPassword' => 'vylrvrvcbolbgutv', 
            'smtpSecure' => false
        ];
        
        error_log("=== CONFIGURACIÓN EMAIL CARGADA ===");
        error_log("From: {$this->config['fromEmail']}");
        error_log("SMTP: {$this->config['smtpHost']}:{$this->config['smtpPort']}");
    }
    
    public function enviarEmail($destinatario, $asunto, $mensaje, $esHTML = true)
    {
        try {
            error_log("=== INTENTANDO ENVIAR EMAIL ===");
            error_log("Destinatario: " . $destinatario);
            error_log("Asunto: " . $asunto);
            
            // ✅ VERIFICAR CONTRASEÑA
            if (empty($this->config['smtpPassword'])) {
                throw new Exception("Contraseña SMTP no configurada");
            }
            
            $mail = new PHPMailer(true);
            
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['smtpHost'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtpUsername'];
            $mail->Password = $this->config['smtpPassword'];
            $mail->SMTPSecure = $this->config['smtpSecure'];
            $mail->Port = $this->config['smtpPort'];
            
            // Debug
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer: $str");
            };
            
            // Remitente
            $mail->setFrom($this->config['fromEmail'], $this->config['fromName']);
            $mail->addAddress($destinatario);
            
            // Contenido
            $mail->isHTML($esHTML);
            $mail->Subject = $asunto;
            $mail->Body = $this->construirPlantillaHTML($mensaje);
            $mail->AltBody = strip_tags($mensaje);
            $mail->CharSet = 'UTF-8';
            
            $mail->send();
            
            error_log("✅ Email enviado exitosamente a: " . $destinatario);
            return ['success' => true, 'message' => 'Email enviado correctamente'];
            
        } catch (Exception $e) {
            $error = "❌ Error enviando email: " . $e->getMessage();
            error_log($error);
            return ['success' => false, 'message' => $error];
        }
    }
    
    private function construirPlantillaHTML($contenido)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                .header { text-align: center; border-bottom: 3px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
                .footer { text-align: center; font-size: 12px; color: #666; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div style="color: #007bff; font-size: 28px; font-weight: bold;">🚀 GoCleaning</div>
                    <h2>Sistema de Gestión de Lavandería</h2>
                </div>
                <div class="content">' . $contenido . '</div>
                <div class="footer">
                    <p>Este es un email automático - No responder</p>
                    <p>&copy; ' . date('Y') . ' Go Cleaning</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    public function enviarCredencialesTemporales($email, $usuario, $claveTemp)
    {
        $asunto = "🔐 Credenciales temporales - Sistema Go Cleaning";
        $mensaje = "
        <div style='padding: 20px; background-color: #e8f5e8; border: 1px solid #4caf50; border-radius: 5px;'>
            <h2>¡Bienvenido al Sistema Go Cleaning!</h2>
            <p>Se ha creado una cuenta para usted:</p>
            <p><strong>👤 Usuario:</strong> {$usuario}</p>
            <p><strong>🔑 Contraseña temporal:</strong></p>
            <div style='font-size: 32px; font-weight: bold; color: #007bff; text-align: center; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; border: 2px dashed #007bff;'>{$claveTemp}</div>
            <p><strong>⏰ Esta contraseña expira en 24 horas.</strong></p>
        </div>
        ";
        
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
    
    public function enviarCodigoOTP($email, $otp)
    {
        $asunto = "🔒 Código de verificación - Sistema Go Cleaning";
        $mensaje = "
        <div style='padding: 20px; background-color: #e8f5e8; border: 1px solid #4caf50; border-radius: 5px;'>
            <h2>Recuperación de Contraseña</h2>
            <p>Su código de verificación es:</p>
            <div style='font-size: 32px; font-weight: bold; color: #007bff; text-align: center; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; border: 2px dashed #007bff;'>{$otp}</div>
            <p>⏳ Este código expira en 15 minutos.</p>
        </div>
        ";
        
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
    
}
?>