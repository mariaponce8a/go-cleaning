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
                    return json_encode(array("perfil" => $data_nav_token['perfil'], "usuario" => $data_nav_token['usuario']));
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

    public function actualizarUsuario($id, $nombre, $apellido, $perfil, $usuario, $clave)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $clave_cifrada_ingresada = hash('sha256', $clave);
            $query = "update tb_usuarios_plataforma set usuario = ?, nombre =?, apellido=?, perfil=? , clave=? WHERE id_usuario= ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ssssss", $usuario, $nombre, $apellido, $perfil, $clave_cifrada_ingresada, $id);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                error_log("?????????????????????RESULTADO INSERT DESDE MODEL " . $resultado);
                return true;
            } else {
                throw new Exception("Problemas al actualizar el usuario");
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
}
