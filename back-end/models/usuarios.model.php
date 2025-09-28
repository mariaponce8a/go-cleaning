<?php
require_once('./back-end/config/conexion.php');    
require_once('./back-end/utils/EmailService.php');

 

class usuarios_model
{   

    public function iniciarSesion($usuario, $clave)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $clave_cifrada_ingresada = hash('sha256', $clave);
            $consulta = "SELECT * FROM tb_usuarios_plataforma WHERE usuario = ? AND clave = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("ss", $usuario, $clave_cifrada_ingresada);
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                
                if ($resultado->num_rows > 0) {
                    $data_nav_token = $resultado->fetch_assoc();
                    
                    // MANEJO CORRECTO DE primer_inicio (puede ser NULL)
                    $primer_inicio = $data_nav_token['primer_inicio'];
                    
                    if ($primer_inicio === null) {
                        $primer_inicio = 0; // O 1
                    }
                    
                    // Asegurarse de que es un integer
                    $primer_inicio = (int)$primer_inicio;
                    
                    error_log("🔐 Login exitoso - Usuario: {$usuario}, Primer inicio: {$primer_inicio}");
                    
                    // Devolver JSON con datos
                    return json_encode(array(
                        "perfil" => $data_nav_token['perfil'], 
                        "usuario" => $data_nav_token['usuario'], 
                        "id_usuario" => $data_nav_token['id_usuario'],
                        "primer_inicio" => $primer_inicio, 
                        "nombre" => $data_nav_token['nombre'],
                        "apellido" => $data_nav_token['apellido']
                    ));
                } else {
                    throw new Exception("Usuario o clave incorrectos.");
    }
            }
        } catch (Exception $e) {
            error_log("Error en login desde modelo: " . $e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

   public function getAllUsers()
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "select id_usuario, usuario, nombre, apellido, perfil, email, cuenta_verificada, primer_inicio from tb_usuarios_plataforma";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar los usuarios");
            } else {
                $users = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $users[] = $fila;
                }
                return json_encode($users);
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

   
    public function getUserById($id_usuario)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Consulta para obtener los datos del usuario sin incluir la contraseña
        $query = "SELECT id_usuario, usuario, nombre, apellido, email, perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_usuario);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                $usuario_data = $resultado->fetch_assoc();
                return json_encode(array(
                    "success" => true, 
                    "data" => $usuario_data
                ));
            } else {
                throw new Exception("Usuario no encontrado.");
            }
        } else {
            throw new Exception("Error al ejecutar la consulta.");
        }
        
    } catch (Exception $e) {
        error_log("Error en getUserById: " . $e->getMessage());
        return json_encode(array(
            "success" => false, 
            "message" => $e->getMessage()
        ));
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}

   public function actualizarUsuario($id, $nombre, $apellido, $usuario, $email, $clave_actual = null) {  // $perfil y $clave removidos o opcionales
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Primero, obtener datos actuales del usuario para verificar clave y perfil
        $query_select = "SELECT clave, perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
        $stmt_select = $conexion->prepare($query_select);
        $stmt_select->bind_param("i", $id);  
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        
        if ($result->num_rows === 0) {
            return ['respuesta' => '0', 'mensaje' => 'Usuario no encontrado'];
        }
        
        $row = $result->fetch_assoc();
        $clave_hash_actual = $row['clave'];
        $perfil_actual = $row['perfil'];  // Mantener el perfil actual
        
        $stmt_select->close();
        
        // Verificar clave actual si se proporciona (para seguridad)
        if ($clave_actual !== null && $clave_actual !== '') {
            $clave_hash_verificacion = hash('sha256', $clave_actual);
            if ($clave_hash_verificacion !== $clave_hash_actual) {
                return ['respuesta' => '0', 'mensaje' => 'Contraseña actual incorrecta'];
            }
        } else {
            return ['respuesta' => '0', 'mensaje' => 'Debe proporcionar la contraseña actual para verificar identidad'];
        }
        
        // Query de actualización: Solo actualiza nombre, apellido, usuario. Mantiene clave y perfil
        $query = "UPDATE tb_usuarios_plataforma SET usuario = ?, nombre = ?, apellido = ?, email = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssssi", $usuario, $nombre, $apellido, $email, $id);  

        if ($stmt->execute()) {
            error_log("Usuario actualizado exitosamente: ID $id");
            return ['respuesta' => '1', 'mensaje' => 'Usuario actualizado con éxito'];
        } else {
            throw new Exception("Problemas al actualizar el usuario");
        }
        
    } catch (Exception $e) {
        error_log("Error en actualizarUsuario: " . $e->getMessage());
        return ['respuesta' => '0', 'mensaje' => 'Error al actualizar: ' . $e->getMessage()];
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

    public function cambiarClave($id_usuario, $clave_actual, $clave_nueva, $confirmar_clave)
    {
        try {
            // Validar que la nueva clave y confirmación sean iguales
            if ($clave_nueva !== $confirmar_clave) {
                throw new Exception("La nueva contraseña y la confirmación no coinciden.");
            }

            // Validar longitud mínima de la nueva contraseña
            if (strlen($clave_nueva) < 6) {
                throw new Exception("La nueva contraseña debe tener al menos 6 caracteres.");
            }

            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            // Primero verificar la contraseña actual
            $clave_actual_cifrada = hash('sha256', $clave_actual);
            $consulta_verificar = "SELECT id_usuario FROM tb_usuarios_plataforma WHERE id_usuario = ? AND clave = ?";
            $stmt_verificar = $conexion->prepare($consulta_verificar);
            $stmt_verificar->bind_param("is", $id_usuario, $clave_actual_cifrada);
            
            if ($stmt_verificar->execute()) {
                $resultado = $stmt_verificar->get_result();
                
                if ($resultado->num_rows === 0) {
                    throw new Exception("La contraseña actual es incorrecta.");
                }
                
                // Si la contraseña actual es correcta, proceder con el cambio
                $clave_nueva_cifrada = hash('sha256', $clave_nueva);
                $query_actualizar = "UPDATE tb_usuarios_plataforma SET clave = ? WHERE id_usuario = ?";
                $stmt_actualizar = $conexion->prepare($query_actualizar);
                $stmt_actualizar->bind_param("si", $clave_nueva_cifrada, $id_usuario);
                
                if ($stmt_actualizar->execute()) {
                    error_log("Contraseña actualizada exitosamente para usuario ID: " . $id_usuario);
                    return json_encode(array("success" => true, "message" => "Contraseña actualizada exitosamente."));
                } else {
                    throw new Exception("Error al actualizar la contraseña en la base de datos.");
                }
                
            } else {
                throw new Exception("Error al verificar la contraseña actual.");
            }
            
        } catch (Exception $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            return json_encode(array("success" => false, "message" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }
    
    public function actualizarPerfilUsuario($id_usuario, $nuevo_perfil, $usuario_admin)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            // Primero, verificar que el usuario que ejecuta la acción es administrador (perfil 'A')
            $query_verificar_admin = "SELECT perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
            $stmt_verificar = $conexion->prepare($query_verificar_admin);
            $stmt_verificar->bind_param("i", $usuario_admin);
            
            if (!$stmt_verificar->execute()) {
                throw new Exception("Error al verificar permisos de administrador.");
            }
            
            $resultado_verificar = $stmt_verificar->get_result();
            
            if ($resultado_verificar->num_rows === 0) {
                throw new Exception("Usuario administrador no encontrado.");
            }
            
            $admin_data = $resultado_verificar->fetch_assoc();
            
            // Verificar que el usuario que ejecuta la acción tenga perfil de administrador ('A')
            if ($admin_data['perfil'] !== 'A') {
                throw new Exception("No tiene permisos para realizar esta acción. Se requiere perfil de administrador.");
            }
            
            $stmt_verificar->close();
            
            // Verificar que el usuario a modificar existe
            $query_verificar_usuario = "SELECT id_usuario, usuario, perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
            $stmt_usuario = $conexion->prepare($query_verificar_usuario);
            $stmt_usuario->bind_param("i", $id_usuario);
            
            if (!$stmt_usuario->execute()) {
                throw new Exception("Error al verificar usuario.");
            }
            
            $resultado_usuario = $stmt_usuario->get_result();
            
            if ($resultado_usuario->num_rows === 0) {
                throw new Exception("Usuario a modificar no encontrado.");
            }
            
            $usuario_data = $resultado_usuario->fetch_assoc();
            $stmt_usuario->close();
            
            // Validar que el nuevo perfil sea válido ('A' o 'E')
            $perfiles_validos = ['A', 'E'];
            if (!in_array($nuevo_perfil, $perfiles_validos)) {
                throw new Exception("Perfil no válido. Perfiles permitidos: A (Administrador) o E (Empleado).");
            }
            
            // Prevenir que un administrador se quite sus propios permisos de administrador
            if ($id_usuario == $usuario_admin && $nuevo_perfil !== 'A') {
                throw new Exception("No puede quitarse sus propios permisos de administrador.");
            }
            
            // Actualizar solo el perfil del usuario
            $query_actualizar = "UPDATE tb_usuarios_plataforma SET perfil = ? WHERE id_usuario = ?";
            $stmt_actualizar = $conexion->prepare($query_actualizar);
            $stmt_actualizar->bind_param("si", $nuevo_perfil, $id_usuario);

            if ($stmt_actualizar->execute()) {
                // Registrar la acción en un log
                error_log("Perfil actualizado por administrador ID: $usuario_admin - Usuario modificado ID: $id_usuario - Nuevo perfil: $nuevo_perfil");
                
                return json_encode(array(
                    "success" => true, 
                    "message" => "Perfil actualizado exitosamente.",
                    "data" => array(
                        "id_usuario" => $id_usuario,
                        "usuario" => $usuario_data['usuario'],
                        "perfil_anterior" => $usuario_data['perfil'],
                        "nuevo_perfil" => $nuevo_perfil,
                        "perfil_texto" => $nuevo_perfil == 'A' ? 'ADMINISTRADOR' : 'EMPLEADO',
                        "actualizado_por" => $usuario_admin
                    )
                ));
            } else {
                throw new Exception("Error al ejecutar la actualización del perfil.");
            }
            
        } catch (Exception $e) {
            error_log("Error en actualizarPerfilUsuario: " . $e->getMessage());
            return json_encode(array(
                "success" => false, 
                "message" => $e->getMessage()
            ));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function registrarUsuario($nombre, $apellido, $perfil, $usuario, $email)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Verificar si el usuario o email ya existen
        $consulta_verificar = "SELECT id_usuario FROM tb_usuarios_plataforma WHERE usuario = ? OR email = ?";
        $stmt_verificar = $conexion->prepare($consulta_verificar);
        $stmt_verificar->bind_param("ss", $usuario, $email);
        
        if ($stmt_verificar->execute()) {
            $resultado_verificar = $stmt_verificar->get_result();
            if ($resultado_verificar->num_rows > 0) {
                throw new Exception("El usuario o email ya existe.");
            }
        }
        
        // Generar contraseña temporal
        $clave_temporal = $this->generarClaveAleatoria(8);
        $clave_temporal_cifrada = hash('sha256', $clave_temporal);
        
        // También cifrar la misma clave temporal para el campo 'clave'
        $clave_cifrada = $clave_temporal_cifrada;
        
        $expiracion = date('Y-m-d H:i:s', strtotime('+24 hours')); // Expira en 24 horas
        
        // CORREGIR: Incluir el campo 'clave' en la consulta
        $query = "INSERT INTO tb_usuarios_plataforma (usuario, nombre, apellido, perfil, email, clave, clave_temporal, clave_temporal_expiracion, primer_inicio, cuenta_verificada) VALUES (?,?,?,?,?,?,?,?,1,1)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssssssss", $usuario, $nombre, $apellido, $perfil, $email, $clave_cifrada, $clave_temporal_cifrada, $expiracion);

        if ($stmt->execute()) {
            // Enviar email con contraseña temporal
            $emailService = new EmailService();
            $subject = "Bienvenido - Credenciales de acceso temporales";
            $message = "
            <h2>Bienvenido al sistema</h2>
            <p>Se ha creado una cuenta para usted con las siguientes credenciales temporales:</p>
            <p><strong>Usuario:</strong> {$usuario}</p>
            <p><strong>Contraseña temporal:</strong> {$clave_temporal}</p>
            <p><strong>Esta contraseña expira en 24 horas.</strong></p>
            <p>Por seguridad, deberá cambiar su contraseña en el primer inicio de sesión.</p>
            <p>Acceda al sistema lo antes posible.</p>
            ";
            
            $emailSent = $emailService->enviarEmail($email, $subject, $message);
            
            if (!$emailSent) {
                error_log("Error al enviar email de bienvenida a: " . $email);
            }
            
            return true;
        } else {
            throw new Exception("Problemas al registrar el usuario: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return $e->getMessage();
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }    
    }
}

    // Cambio de contraseña para primer inicio 
    public function cambiarClaveInicial($id_usuario, $clave_temporal, $nueva_clave, $confirmar_clave)
{
    try {
        if ($nueva_clave !== $confirmar_clave) {
            throw new Exception("La nueva contraseña y la confirmación no coinciden.");
        }

        if (strlen($nueva_clave) < 6) {
            throw new Exception("La nueva contraseña debe tener al menos 6 caracteres.");
        }

        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Verificar contraseña temporal
        $clave_temporal_cifrada = hash('sha256', $clave_temporal);
        $consulta_verificar = "SELECT id_usuario FROM tb_usuarios_plataforma WHERE id_usuario = ? AND clave_temporal = ? AND clave_temporal_expiracion > NOW()";
        $stmt_verificar = $conexion->prepare($consulta_verificar);
        $stmt_verificar->bind_param("is", $id_usuario, $clave_temporal_cifrada);
        
        if ($stmt_verificar->execute()) {
            $resultado = $stmt_verificar->get_result();
            
            if ($resultado->num_rows === 0) {
                throw new Exception("La contraseña temporal es incorrecta o ha expirado.");
            }
            
            //Actualizar con nueva contraseña y marcar primer_inicio como completado
            $nueva_clave_cifrada = hash('sha256', $nueva_clave);
            $query_actualizar = "UPDATE tb_usuarios_plataforma SET 
                                clave = ?, 
                                clave_temporal = NULL, 
                                clave_temporal_expiracion = NULL, 
                                primer_inicio = 0  -- AQUÍ SE ACTUALIZA A 0
                                WHERE id_usuario = ?";
            $stmt_actualizar = $conexion->prepare($query_actualizar);
            $stmt_actualizar->bind_param("si", $nueva_clave_cifrada, $id_usuario);
            
            if ($stmt_actualizar->execute()) {
                return json_encode(array("success" => true, "message" => "Contraseña establecida exitosamente."));
            } else {
                throw new Exception("Error al establecer la nueva contraseña.");
            }
            
        } else {
            throw new Exception("Error al verificar la contraseña temporal.");
        }
        
    } catch (Exception $e) {
        error_log("Error al cambiar contraseña inicial: " . $e->getMessage());
        return json_encode(array("success" => false, "message" => $e->getMessage()));
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}
    // Solicitar recuperación de contraseña por OTP
    public function solicitarRecuperacionClave($email_o_usuario)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            // Buscar usuario por email o nombre de usuario
            $consulta = "SELECT id_usuario, email, usuario FROM tb_usuarios_plataforma WHERE email = ? OR usuario = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("ss", $email_o_usuario, $email_o_usuario);
            
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                
                if ($resultado->num_rows === 0) {
                    throw new Exception("No se encontró ninguna cuenta asociada con esa información.");
                }
                
                $usuario_data = $resultado->fetch_assoc();
                
                // Generar OTP de 6 dígitos
                $otp = sprintf("%06d", mt_rand(0, 999999));
                $otp_expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Expira en 15 minutos
                
                // Actualizar OTP en la base de datos
                $query_otp = "UPDATE tb_usuarios_plataforma SET otp = ?, otp_expiracion = ? WHERE id_usuario = ?";
                $stmt_otp = $conexion->prepare($query_otp);
                $stmt_otp->bind_param("ssi", $otp, $otp_expiracion, $usuario_data['id_usuario']);
                
                if ($stmt_otp->execute()) {
                    // Enviar OTP por email
                    $emailService = new EmailService();
                    $subject = "Código de recuperación de contraseña";
                    $message = "
                    <h2>Recuperación de contraseña</h2>
                    <p>Ha solicitado recuperar su contraseña.</p>
                    <p><strong>Su código de verificación es:</strong> <span style='font-size:24px; font-weight:bold; color:#007bff;'>{$otp}</span></p>
                    <p>Este código expira en 15 minutos.</p>
                    <p>Si no solicitó este código, ignore este mensaje.</p>
                    ";
                    
                    $emailSent = $emailService->enviarEmail($usuario_data['email'], $subject, $message);
                    
                    if ($emailSent) {
                        return json_encode(array(
                            "success" => true, 
                            "message" => "Código de verificación enviado al email registrado.",
                            "masked_email" => $this->enmascararEmail($usuario_data['email'])
                        ));
                    } else {
                        throw new Exception("Error al enviar el código de verificación.");
                    }
                    
                } else {
                    throw new Exception("Error al generar el código de verificación.");
                }
                
            } else {
                throw new Exception("Error al buscar la cuenta.");
            }
            
        } catch (Exception $e) {
            error_log("Error en solicitar recuperación: " . $e->getMessage());
            return json_encode(array("success" => false, "message" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Verificar OTP y permitir cambio de contraseña
    public function verificarOTPyResetearClave($email_o_usuario, $otp, $nueva_clave, $confirmar_clave)
    {
        try {
            if ($nueva_clave !== $confirmar_clave) {
                throw new Exception("La nueva contraseña y la confirmación no coinciden.");
            }

            if (strlen($nueva_clave) < 6) {
                throw new Exception("La nueva contraseña debe tener al menos 6 caracteres.");
            }

            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            // Verificar OTP
            $consulta = "SELECT id_usuario, email FROM tb_usuarios_plataforma WHERE (email = ? OR usuario = ?) AND otp = ? AND otp_expiracion > NOW()";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("sss", $email_o_usuario, $email_o_usuario, $otp);
            
            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                
                if ($resultado->num_rows === 0) {
                    throw new Exception("Código de verificación incorrecto o expirado.");
                }
                
                $usuario_data = $resultado->fetch_assoc();
                
                // Actualizar contraseña y limpiar OTP
                $nueva_clave_cifrada = hash('sha256', $nueva_clave);
                $query_reset = "UPDATE tb_usuarios_plataforma SET clave = ?, otp = NULL, otp_expiracion = NULL, clave_temporal = NULL, clave_temporal_expiracion = NULL WHERE id_usuario = ?";
                $stmt_reset = $conexion->prepare($query_reset);
                $stmt_reset->bind_param("si", $nueva_clave_cifrada, $usuario_data['id_usuario']);
                
                if ($stmt_reset->execute()) {
                    // Notificar por email del cambio de contraseña
                    $emailService = new EmailService();
                    $subject = "Contraseña restablecida exitosamente";
                    $message = "
                    <h2>Contraseña restablecida</h2>
                    <p>Su contraseña ha sido restablecida exitosamente.</p>
                    <p>Si no realizó esta acción, contacte al administrador inmediatamente.</p>
                    <p>Fecha: " . date('Y-m-d H:i:s') . "</p>
                    ";
                    
                    $emailService->enviarEmail($usuario_data['email'], $subject, $message);
                    
                    return json_encode(array("success" => true, "message" => "Contraseña restablecida exitosamente."));
                } else {
                    throw new Exception("Error al restablecer la contraseña.");
                }
                
            } else {
                throw new Exception("Error al verificar el código.");
            }
            
        } catch (Exception $e) {
            error_log("Error en verificar OTP y resetear: " . $e->getMessage());
            return json_encode(array("success" => false, "message" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    // Funciones auxiliares
    private function generarClaveAleatoria($longitud = 8)
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle($caracteres), 0, $longitud);
    }

    private function enmascararEmail($email)
    {
        $partes = explode('@', $email);
        $usuario = $partes[0];
        $dominio = $partes[1];
        
        $usuario_enmascarado = substr($usuario, 0, 2) . str_repeat('*', strlen($usuario) - 2);
        return $usuario_enmascarado . '@' . $dominio;
    }

 
}