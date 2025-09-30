<?php
require_once('./back-end/models/estadisticas.model.php');

class Estadisticas_controller
{    
    public function getServicioMasSolicitado($periodo)
    {
        error_log("-------------- GET SERVICIO MAS SOLICITADO --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($periodo === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Período no especificado"));
        }
        
        $resultado = $estadisticasModel->getServicioMasSolicitado($periodo);
        error_log("---------- RESULTADO SERVICIO MAS SOLICITADO DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        
        // Verificar si hay error o si es un array vacío
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los servicios más solicitados"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Servicios más solicitados cargados con éxito", 
                "data" => $resultadoArray  // Ahora es un array, no un objeto individual
            ));
        }
    }

    public function getTopClientes($periodo)
    {
        error_log("-------------- GET TOP CLIENTES --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($periodo === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Período no especificado"));
        }
        
        $resultado = $estadisticasModel->getTopClientes($periodo);
        error_log("---------- RESULTADO TOP CLIENTES DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar los mejores clientes"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Mejores clientes cargados con éxito", 
                "data" => $resultadoArray
            ));
        }
    }

    public function getControlCaja($periodo)
    {
        error_log("-------------- GET CONTROL CAJA --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($periodo === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Período no especificado"));
        }
        
        $resultado = $estadisticasModel->getControlCaja($periodo);
        error_log("---------- RESULTADO CONTROL CAJA DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar el control de caja"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Control de caja cargado con éxito", 
                "data" => $resultadoArray
            ));
        }
    }

    public function getEstadisticasGenerales($periodo)
    {
        error_log("-------------- GET ESTADISTICAS GENERALES --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($periodo === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Período no especificado"));
        }
        
        $resultado = $estadisticasModel->getEstadisticasGenerales($periodo);
        error_log("---------- RESULTADO ESTADISTICAS GENERALES DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar las estadísticas generales"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Estadísticas generales cargadas con éxito", 
                "data" => $resultadoArray
            ));
        }
    }

    public function getVentasPorMes($anio)
    {
        error_log("-------------- GET VENTAS POR MES --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($anio === null) {
            $anio = date('Y');
        }
        
        $resultado = $estadisticasModel->getVentasPorMes($anio);
        error_log("---------- RESULTADO VENTAS POR MES DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar las ventas por mes"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Ventas por mes cargadas con éxito", 
                "data" => $resultadoArray
            ));
        }
    }

    public function getAllEstadisticas($periodo)
    {
        error_log("-------------- GET TODAS LAS ESTADISTICAS --------------");
        $estadisticasModel = new Clase_Estadisticas();
        
        if ($periodo === null) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Período no especificado"));
        }
        
        $resultado = $estadisticasModel->getAllEstadisticas($periodo);
        error_log("---------- RESULTADO TODAS ESTADISTICAS DESDE CONTROLLER: " . $resultado);
        
        $resultadoArray = json_decode($resultado, true);
        if (isset($resultadoArray['error'])) {
            return json_encode(array("respuesta" => "0", "mensaje" => "Problemas para cargar las estadísticas"));
        } else {
            return json_encode(array(
                "respuesta" => "1", 
                "mensaje" => "Todas las estadísticas cargadas con éxito", 
                "data" => $resultadoArray
            ));
        }
    }
}
?>