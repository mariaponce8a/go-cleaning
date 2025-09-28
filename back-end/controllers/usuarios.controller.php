<?php
require_once('./back-end/models/usuarios.model.php');

class Usuarios_controller
{
   /* public function validateLogin($usuario, $clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if ($usuario === null  || $clave === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar sus credenciales"));
        }
        error_log($usuario);
        error_log($clave);
        $resultado = $usuarioModel->iniciarSesion($usuario, $clave);
        if ($resultado === "Usuario o clave incorrectos.") {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "OK", "data" => json_decode($resultado)));
        
        }
    }*/

   public function validateLogin($usuario, $clave)
{
    error_log("-------------- Validando login --------------");
    
    if ($usuario === null || $clave === null || $usuario === '' || $clave === '') {
        return json_encode(array("respuesta" => "0", "mensaje" => "Debe ingresar sus credenciales"));
    }
    
    error_log("Usuario: " . $usuario);
    error_log("Clave: " . substr($clave, 0, 3) . "...");
    
    try {
        $usuarioModel = new usuarios_model();
        $resultado = $usuarioModel->iniciarSesion($usuario, $clave);
        
        // VERIFICAR SI ES UN ERROR DE CONEXIÓN (string en lugar de JSON)
        if (!json_decode($resultado) && (strpos($resultado, 'exceeded') !== false || strpos($resultado, 'max_user_connections') !== false)) {
            error_log("Error de conexión detectado: " . $resultado);
            return json_encode(array(
                "respuesta" => "0", 
                "mensaje" => "El sistema está ocupado. Por favor, intente nuevamente en unos momentos."
            ));
        }
        
        // Decodificar la respuesta JSON del modelo
        $respuestaModelo = json_decode($resultado, true);
        
        // Verificar si la decodificación falló
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Error decodificando JSON: " . json_last_error_msg());
            error_log("Respuesta cruda: " . $resultado);
            return json_encode(array(
                "respuesta" => "0", 
                "mensaje" => "Error en el formato de respuesta del servidor"
            ));
        }
        
        // Verificar si hay error en la respuesta del modelo
        if (isset($respuestaModelo['error'])) {
            error_log("Error en login: " . $respuestaModelo['error']);
            return json_encode(array("respuesta" => "0", "mensaje" => $respuestaModelo['error']));
        }
        
        // Asegurar que la respuesta tenga la estructura esperada
        if (!is_array($respuestaModelo)) {
            $respuestaModelo = [];
        }
        
        // Estructura mínima requerida
        $dataCompleta = array_merge([
            'primer_inicio' => '0',
            'token' => '',
            'usuario' => $usuario,
            'perfil' => '',
            'id_usuario' => ''
        ], $respuestaModelo);
        
        // Login exitoso
        error_log("Login exitoso para usuario: " . $usuario);
        return json_encode(array(
            "respuesta" => "1", 
            "mensaje" => "OK", 
            "data" => $dataCompleta
        ));
        
    } catch (Exception $e) {
        error_log("Excepción en validateLogin: " . $e->getMessage());
        return json_encode(array(
            "respuesta" => "0", 
            "mensaje" => "Error interno del servidor"
        ));
    }
}
    public function getAllUsers()
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        $resultado = $usuarioModel->getAllUsers();
        error_log("----------RESULTADO SELECT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los usuarios"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuarios cargados con éxito", "data" => json_decode($resultado)));
        }
    }

    public function getUserById($id_usuario)
        {
            error_log("--------------");
            error_log("Obteniendo usuario por ID: " . $id_usuario);
            
            $usuarioModel = new usuarios_model();
            
            if ($id_usuario === null) {
                return json_encode(array("respuesta" => "0", "mensaje" => "ID de usuario requerido"));
            }
            
            $resultado = $usuarioModel->getUserById($id_usuario);
            $resultado_decodificado = json_decode($resultado, true);
            
            error_log("----------RESULTADO GET USER BY ID DESDE CONTROLLER: " . $resultado);
            
            if ($resultado_decodificado['success'] == false) {
                return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
            } else {
                return json_encode(array("respuesta" => "1", "mensaje" => "Usuario cargado con éxito", "data" => $resultado_decodificado['data']));
            }
        }
   /* public function insertUser($nombre, $apellido, $perfil, $usuario, $clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if (
            $usuario === null  ||
            $clave === null ||
            $nombre === null  ||
            $apellido === null ||
            $perfil === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }
        $resultado = $usuarioModel->registrarUsuario($nombre, $apellido, $perfil, $usuario, $clave);
        error_log("----------RESULTADO INSERT DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para registrar el usuario"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario registrado con éxito"));
        }
    }*/
    
     public function insertUser($nombre, $apellido, $perfil, $usuario, $email)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        
        if (empty($usuario) || empty($nombre) || empty($apellido) || empty($perfil) || empty($email)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "El email no tiene un formato válido."));
        }
        
        $resultado = $usuarioModel->registrarUsuario($nombre, $apellido, $perfil, $usuario, $email);
        
        if ($resultado === true) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario registrado con éxito. Se han enviado las credenciales por email."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado));
        }
    }

    public function updateUser ($id_usuario, $nombre, $apellido, $usuario, $email, $clave_actual) {
    error_log("-------------- Iniciando updateUser  con params directos");
    
    // Validación (ya hecha en route, pero por seguridad)
    if (empty($nombre) || empty($apellido) || empty($usuario) || empty($email) || empty($clave_actual)) {
        return array("respuesta" => "0", "mensaje" => "Campos requeridos faltantes en controlador.");
    }
    
    $usuarioModel = new usuarios_model();
    $resultado = $usuarioModel->actualizarUsuario($id_usuario, $nombre, $apellido, $usuario, $email, $clave_actual);
    
    error_log("----------RESULTADO UPDATE DESDE CONTROLLER: " . json_encode($resultado));
    
    // Retorna array directamente (el route lo encodeará)
    return $resultado;  // Asume que el modelo retorna ['respuesta' => '1', 'mensaje' => '...']
}

    public function updateUserProfile($id_usuario, $nuevo_perfil, $usuario_admin)
    {
        error_log("-------------- Iniciando updateUserProfile");
        error_log("ID Usuario a modificar: " . $id_usuario);
        error_log("Nuevo perfil: " . $nuevo_perfil);
        error_log("ID Administrador: " . $usuario_admin);
        
        // Validaciones básicas
        if ($id_usuario === null || $nuevo_perfil === null || $usuario_admin === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Datos requeridos faltantes."));
        }
        
        // Validar que el perfil sea A o E
        if (!in_array($nuevo_perfil, ['A', 'E'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Perfil no válido. Solo se permiten A (Administrador) o E (Empleado)."));
        }
        
        $usuarioModel = new usuarios_model();
        $resultado = $usuarioModel->actualizarPerfilUsuario($id_usuario, $nuevo_perfil, $usuario_admin);
        $resultado_decodificado = json_decode($resultado, true);
        
        error_log("----------RESULTADO UPDATE PERFIL DESDE CONTROLLER: " . $resultado);
        
        if ($resultado_decodificado['success'] == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Perfil actualizado con éxito",
                "data" => $resultado_decodificado['data']
            ));
        }
    }

    public function deleteUsuario($id)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        if (
            $id === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Usuario no seleccionado"));
        }
        $resultado = $usuarioModel->eliminarUsuario($id);
        error_log("----------RESULTADO DELETE DESDE CONTROLLER: " . $resultado);
        if ($resultado == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para eliminar el usuario"));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Usuario eliminado con éxito"));
        }
    }


    public function changePassword($id_usuario, $clave_actual, $clave_nueva, $confirmar_clave)
    {
        error_log("--------------");
        $usuarioModel = new usuarios_model();
        
        if (
            $id_usuario === null ||
            $clave_actual === null ||
            $clave_nueva === null ||
            $confirmar_clave === null
        ) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Por favor complete todos los campos."));
        }

        // Validar que las contraseñas nuevas coincidan
        if ($clave_nueva !== $confirmar_clave) {
            return json_encode(array("respuesta" => "0", "mensaje" => "La nueva contraseña y la confirmación no coinciden."));
        }

        // Validar longitud mínima
        if (strlen($clave_nueva) < 6) {
            return json_encode(array("respuesta" => "0", "mensaje" => "La nueva contraseña debe tener al menos 6 caracteres."));
        }

        error_log("------ Cambiando contraseña para usuario ID: " . $id_usuario);
        
        $resultado = $usuarioModel->cambiarClave($id_usuario, $clave_actual, $clave_nueva, $confirmar_clave);
        $resultado_decodificado = json_decode($resultado, true);
        
        error_log("----------RESULTADO CAMBIO CONTRASEÑA DESDE CONTROLLER: " . $resultado);
        
        if ($resultado_decodificado['success'] == false) {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
        } else {
            return json_encode(array("respuesta" => "1", "mensaje" => "Contraseña actualizada con éxito"));
        }
    }

    public function setInitialPassword($id_usuario, $clave_temporal, $nueva_clave, $confirmar_clave)
    {
        error_log("-------------- Estableciendo contraseña inicial");
        $usuarioModel = new usuarios_model();
        
        if (empty($id_usuario) || empty($clave_temporal) || empty($nueva_clave) || empty($confirmar_clave)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos."));
        }

        $resultado = $usuarioModel->cambiarClaveInicial($id_usuario, $clave_temporal, $nueva_clave, $confirmar_clave);
        $resultado_decodificado = json_decode($resultado, true);
        
        if ($resultado_decodificado['success']) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Contraseña establecida exitosamente. Ya puede usar su nueva contraseña."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
        }
    }

    // Solicitar recuperación por OTP
    public function requestPasswordReset($email_o_usuario)
    {
        error_log("-------------- Solicitando recuperación de contraseña");
        $usuarioModel = new usuarios_model();
        
        if (empty($email_o_usuario)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Debe proporcionar su email o usuario."));
        }

        $resultado = $usuarioModel->solicitarRecuperacionClave($email_o_usuario);
        $resultado_decodificado = json_decode($resultado, true);
        
        if ($resultado_decodificado['success']) {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Código enviado exitosamente.",
                "data" => array("masked_email" => $resultado_decodificado['masked_email'] ?? "")
            ));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
        }
    }

    // Verificar OTP y resetear contraseña
    public function resetPasswordWithOTP($email_o_usuario, $otp, $nueva_clave, $confirmar_clave)
    {
        error_log("-------------- Verificando OTP y reseteando contraseña");
        $usuarioModel = new usuarios_model();
        
        if (empty($email_o_usuario) || empty($otp) || empty($nueva_clave) || empty($confirmar_clave)) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Todos los campos son requeridos."));
        }

        $resultado = $usuarioModel->verificarOTPyResetearClave($email_o_usuario, $otp, $nueva_clave, $confirmar_clave);
        $resultado_decodificado = json_decode($resultado, true);
        
        if ($resultado_decodificado['success']) {
            return json_encode(array("respuesta" => "1", "mensaje" => "Contraseña restablecida exitosamente."));
        } else {
            return json_encode(array("respuesta" => "0", "mensaje" => $resultado_decodificado['message']));
        }
    }
    

}
