<?php
require_once('./back-end/config/conexion.php'); 

class Clase_Estadisticas
{
   public function getServicioMasSolicitado($periodo)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        $filtroFecha = $this->getFiltroFecha($periodo);
        
        $query = "
            SELECT 
                s.descripcion_servicio,
                COUNT(pd.fk_id_servicio) as total_solicitudes,
                SUM(pd.cantidad) as total_articulos,
                COALESCE(SUM(
                    (pd.precio_servicio / NULLIF(p.pedido_subtotal, 0)) * p.total
                ), 0) as ingresos_generados
            FROM tb_pedido_detalle pd
            INNER JOIN tb_servicios s ON pd.fk_id_servicio = s.id_servicio
            INNER JOIN tb_pedido p ON pd.fk_id_pedido = p.id_pedido_cabecera
            WHERE p.estado_pedido = 1 
            AND p.estado_pago IN ('P', 'C')
            AND p.fecha_pedido $filtroFecha
            GROUP BY s.id_servicio, s.descripcion_servicio
            ORDER BY total_solicitudes DESC, total_articulos DESC
            LIMIT 5
        ";
        
        $exeResult = mysqli_query($conexion, $query);

        if ($exeResult == false) {
            throw new Exception("Problemas al cargar los servicios más solicitados");
        } else {
            $servicios = array();
            while ($fila = mysqli_fetch_assoc($exeResult)) {
                $servicios[] = $fila;
            }
            
            // Si no hay datos, retornar array vacío
            return json_encode($servicios);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return json_encode(array("error" => $e->getMessage()));
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}
    public function getTopClientes($periodo, $limite = 5)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $filtroFecha = $this->getFiltroFecha($periodo);
            
            $query = "
                SELECT 
                    c.id_cliente,
                    c.nombre_cliente,
                    c.apellido_cliente,
                    c.identificacion_cliente,
                    COUNT(p.id_pedido_cabecera) as total_pedidos,
                    SUM(p.total) as total_gastado
                FROM tb_clientes_registrados c
                INNER JOIN tb_pedido p ON c.id_cliente = p.fk_id_cliente
                WHERE p.estado_pedido = 1 
                AND p.fecha_pedido $filtroFecha
                GROUP BY c.id_cliente, c.nombre_cliente, c.apellido_cliente, c.identificacion_cliente
                ORDER BY total_gastado DESC
                LIMIT $limite
            ";
            
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar los mejores clientes");
            } else {
                $clientes = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $clientes[] = $fila;
                }
                return json_encode($clientes);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function getControlCaja($periodo)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $filtroFecha = $this->getFiltroFecha($periodo);
            
            $query = "
                SELECT 
                COUNT(*) as total_pedidos,
                COALESCE(SUM(p.total), 0) as ingresos_totales,
                COALESCE(SUM(p.pedido_subtotal), 0) as subtotal,
                COALESCE(SUM(p.total - p.pedido_subtotal), 0) as impuestos_descuentos,
                COALESCE(AVG(p.total), 0) as promedio_por_pedido
                FROM tb_pedido p
                WHERE p.estado_pedido = 1  
                AND p.estado_pago IN ('P', 'C')
                AND p.fecha_pedido $filtroFecha
            ";
            
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar el control de caja");
            } else {
                $caja = mysqli_fetch_assoc($exeResult);
                return json_encode($caja ?: []);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function getEstadisticasGenerales($periodo)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $filtroFecha = $this->getFiltroFecha($periodo);
            
            $query = "
                SELECT 
                    (SELECT COUNT(*) FROM tb_pedido WHERE estado_pedido = 1 AND fecha_pedido $filtroFecha) as total_pedidos,
                    (SELECT COALESCE(SUM(total), 0) FROM tb_pedido WHERE estado_pedido = 1 AND estado_pago = 'C' AND fecha_pedido $filtroFecha) as ingresos_totales,
                    (SELECT COUNT(DISTINCT fk_id_cliente) FROM tb_pedido WHERE estado_pedido = 1 AND fecha_pedido $filtroFecha) as clientes_atendidos,
                    (SELECT COUNT(*) FROM tb_pedido_detalle pd 
                     INNER JOIN tb_pedido p ON pd.fk_id_pedido = p.id_pedido_cabecera 
                     WHERE p.estado_pedido = 1 AND p.fecha_pedido $filtroFecha) as servicios_realizados
            ";
            
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar las estadísticas generales");
            } else {
                $estadisticas = mysqli_fetch_assoc($exeResult);
                return json_encode($estadisticas ?: []);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function getVentasPorMes($anio)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $query = "
                SELECT 
                    MONTH(fecha_pedido) as mes,
                    COUNT(*) as total_pedidos,
                    COALESCE(SUM(total), 0) as ingresos_totales
                FROM tb_pedido 
                WHERE estado_pedido = 1 
                AND estado_pago = 'C'
                AND YEAR(fecha_pedido) = $anio
                GROUP BY MONTH(fecha_pedido)
                ORDER BY mes
            ";
            
            $exeResult = mysqli_query($conexion, $query);

            if ($exeResult == false) {
                throw new Exception("Problemas al cargar las ventas por mes");
            } else {
                $ventas = array();
                while ($fila = mysqli_fetch_assoc($exeResult)) {
                    $ventas[] = $fila;
                }
                return json_encode($ventas);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(array("error" => $e->getMessage()));
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function getAllEstadisticas($periodo)
{
    try {
        // Obtener cada estadística individualmente
        $serviciosMasSolicitados = json_decode($this->getServicioMasSolicitado($periodo), true);
        $topClientes = json_decode($this->getTopClientes($periodo), true);
        $controlCaja = json_decode($this->getControlCaja($periodo), true);
        $estadisticasGenerales = json_decode($this->getEstadisticasGenerales($periodo), true);
        
        // Verificar errores en cada consulta
        $errores = [];
        if (isset($serviciosMasSolicitados['error'])) $errores[] = "servicios";
        if (isset($topClientes['error'])) $errores[] = "clientes";
        if (isset($controlCaja['error'])) $errores[] = "caja";
        if (isset($estadisticasGenerales['error'])) $errores[] = "generales";
        
        if (!empty($errores)) {
            throw new Exception("Problemas en: " . implode(", ", $errores));
        }
        
        return json_encode(array(
            "servicioMasSolicitado" => $serviciosMasSolicitados,
            "topClientes" => $topClientes,
            "controlCaja" => $controlCaja,
            "estadisticasGenerales" => $estadisticasGenerales
        ));
    } catch (Exception $e) {
        error_log($e->getMessage());
        return json_encode(array("error" => $e->getMessage()));
    }
}

    private function getFiltroFecha($periodo)
    {
        $hoy = date('Y-m-d');
        switch($periodo) {
            case 'dia':
                return ">= '$hoy 00:00:00' AND fecha_pedido <= '$hoy 23:59:59'";
            case 'semana':
                $inicioSemana = date('Y-m-d', strtotime('monday this week'));
                $finSemana = date('Y-m-d', strtotime('sunday this week'));
                return ">= '$inicioSemana 00:00:00' AND fecha_pedido <= '$finSemana 23:59:59'";
            case 'mes':
                $inicioMes = date('Y-m-01');
                $finMes = date('Y-m-t');
                return ">= '$inicioMes 00:00:00' AND fecha_pedido <= '$finMes 23:59:59'";
            case 'año':
                $inicioAnio = date('Y-01-01');
                $finAnio = date('Y-12-31');
                return ">= '$inicioAnio 00:00:00' AND fecha_pedido <= '$finAnio 23:59:59'";
            default:
                return ">= '$hoy 00:00:00' AND fecha_pedido <= '$hoy 23:59:59'";
        }
    }
}
