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
}
