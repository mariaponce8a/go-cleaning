<?php
require_once('./back-end/config/conexion.php');

class Clase_Iva
{
    public function getAllIvas() 
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            $query = "select * FROM tb_iva";
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar los datos");
            } else {
                $iva = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $iva[] = $fila;
                }   

                return json_encode($iva);
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