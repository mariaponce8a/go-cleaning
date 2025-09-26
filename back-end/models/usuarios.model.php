<?php
require_once('./back-end/config/conexion.php');    
 

class usuarios_model
{   

    public function iniciarSesion($usuario, $clave)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $clave_cifrada_ingresada = hash('sha256', $clave);
            $consulta = "SELECT * FROM tb_usuarios_plataforma where usuario = ? and clave = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("ss", $usuario, $clave_cifrada_ingresada);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                // error_log( $resultado);
                if ($resultado->num_rows > 0) {
                    $data_nav_token = $resultado->fetch_assoc();
                    return json_encode(array("perfil" => $data_nav_token['perfil'], "usuario" => $data_nav_token['usuario'], "id_usuario" => $data_nav_token['id_usuario']));
                } else {
                    throw new Exception("Usuario o clave incorrectos.");
                }
            }
        } catch (Exception $e) {
            error_log("Error en login desde modelo: " . $e->getMessage());
            return $e->getMessage();
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
            $query = "select * from tb_usuarios_plataforma";
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
        $query = "SELECT id_usuario, usuario, nombre, apellido, perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
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

    public function registrarUsuario($nombre, $apellido, $perfil, $usuario, $clave)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $clave_cifrada_ingresada = hash('sha256', $clave);
            $query = "insert into tb_usuarios_plataforma (usuario, nombre, apellido, perfil, clave) values (?,?,?,?,?);";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sssss", $usuario, $nombre, $apellido, $perfil, $clave_cifrada_ingresada);

            if ($stmt->execute()) {   
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al registrar el usuario");
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

   public function actualizarUsuario($id, $nombre, $apellido, $usuario, $clave_actual = null) {  // $perfil y $clave removidos o opcionales
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Primero, obtener datos actuales del usuario para verificar clave y perfil
        $query_select = "SELECT clave, perfil FROM tb_usuarios_plataforma WHERE id_usuario = ?";
        $stmt_select = $conexion->prepare($query_select);
        $stmt_select->bind_param("i", $id);  // Asumiendo id es int
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
        $query = "UPDATE tb_usuarios_plataforma SET usuario = ?, nombre = ?, apellido = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("sssi", $usuario, $nombre, $apellido, $id);  // "sssi" para strings y int

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
}