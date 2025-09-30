import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Constantes } from '../../config/constantes';

export interface ServicioMasSolicitado {
  descripcion_servicio: string;
  total_solicitudes: number;
  total_articulos: number;
}

export interface TopCliente {
  id_cliente: number;
  nombre_cliente: string;
  apellido_cliente: string;
  identificacion_cliente: string;
  total_pedidos: number;
  total_gastado: number;
}

export interface ControlCaja {
  total_pedidos: number;
  ingresos_totales: number;
  subtotal: number;
  impuestos_descuentos: number;
  promedio_por_pedido: number;
}

export interface EstadisticasGenerales {
  total_pedidos: number;
  ingresos_totales: number;
  clientes_atendidos: number;
  servicios_realizados: number;
}

export interface VentaPorMes {
  mes: number;
  total_pedidos: number;
  ingresos_totales: number;
}

export interface TodasEstadisticas {
  servicioMasSolicitado: ServicioMasSolicitado;
  topClientes: TopCliente[];
  controlCaja: ControlCaja;
  estadisticasGenerales: EstadisticasGenerales;
}

@Injectable({
  providedIn: 'root'
})
export class EstadisticasService {

  constructor(private http: HttpClient) { }

  getServicioMasSolicitado(periodo: string): Observable<any> {
    let params = new HttpParams().set('periodo', periodo);
    return this.http.get(Constantes.apiEstadisticasServicioMasSolicitado, { params });
  }

  getTopClientes(periodo: string): Observable<any> {
    let params = new HttpParams().set('periodo', periodo);
    return this.http.get(Constantes.apiEstadisticasTopClientes, { params });
  }

  getControlCaja(periodo: string): Observable<any> {
    let params = new HttpParams().set('periodo', periodo);
    return this.http.get(Constantes.apiEstadisticasControlCaja, { params });
  }

  getEstadisticasGenerales(periodo: string): Observable<any> {
    let params = new HttpParams().set('periodo', periodo);
    return this.http.get(Constantes.apiEstadisticasGenerales, { params });
  }

  getVentasPorMes(anio: number): Observable<any> {
    let params = new HttpParams().set('anio', anio.toString());
    return this.http.get(Constantes.apiEstadisticasVentasMes, { params });
  }

  getAllEstadisticas(periodo: string): Observable<any> {
    let params = new HttpParams().set('periodo', periodo);
    return this.http.get(Constantes.apiEstadisticasTodas, { params });
  }
}