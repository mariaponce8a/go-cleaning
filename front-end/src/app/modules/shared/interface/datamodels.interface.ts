export interface ITitulosTabla {
  value: string; //valor visible
  viewValue: string;
}

export interface IaccionBotones {
  tipo: string;
  fila: any;
}

export interface IMenu {
  icon: string;
  pagina: string;
  descripcion: string;
  perfil: string;
}

export interface IusuariosPlataforma {
  id_usuario?: number;
  fk_id_usuario?: string | number;
  usuario: string;
  nombre: string;
  apellido: string;
  perfil: string;
  clave: string;
}

export interface IpedidosJoin {
  id_pedido_cabecera: string;
  fecha_pedido: string;
  fk_id_usuario: string;
  usuario: string;
  cantidad_articulos: string;
  fk_id_cliente: string;
  identificacion_cliente: string;
  correo_cliente: string;
  nombre_cliente: string;
  apellido_cliente: string;
  fk_id_descuentos: string;
  tipo_descuento_desc: string;
  cantidad_descuento: string;
  pedido_subtotal: string;
  estado_pago: string;
  valor_pago: string;
  fecha_hora_recoleccion_estimada: string;
  direccion_recoleccion: string;
  fecha_hora_entrega_estimada: string;
  direccion_entrega: string;
  tipo_entrega: string
}

export interface IServicioPedido {
  fk_id_servicio: number;
  libras: number;
  precio_servicio: number;
  fk_id_pedido: number;
  descripcion_articulo: string;
}

export interface IpedidosRegistro {
  fecha_pedido: string;
  fk_id_usuario: number;
  cantidad_articulos: number;
  fk_id_cliente: number;
  fk_id_descuentos: number;
  pedido_subtotal: number;
  estado_pago: string;
  valor_pago: number;
  fecha_hora_recoleccion_estimada: string;
  direccion_recoleccion: string;
  fecha_hora_entrega_estimada: string;
  direccion_entrega: string;
  tipo_entrega: string
}

export interface IserviciosPlataforma {
  id_servicio: string | number;
  descripcion_servicio: string;
  costo_unitario: number;
  validar_pesaje?: any | string; // llega un numero pero se lo reemplaza por un string
  maximo_articulos?:number | string | null;
}

export interface IclientesPlataforma {
  id_cliente: string | number;
  identificacion_cliente: string;
  tipo_identificacion_cliente: string;
  nombre_cliente: string;
  apellido_cliente: string;
  telefono_cliente: string;
  correo_cliente: string;
}
export interface IdescuentosPlataforma {
  id_tipo_descuento: string | number;
  tipo_descuento_desc: string;
  cantidad_descuento: number;
}
export interface ImaterialesPlataforma {
  id_material: number;
  descripcion_material: string;
}
export interface IestadosPlataforma {
  id_estado: number;
  descripcion_estado: string;
}

export interface IrecomendacionesPlataforma {
  id_recomendacion: number;
  fk_id_material: any;
  fk_id_servicio: any;
}
export interface IAsignacionEmpleadosPlataforma {
  id_asignaciones: string;
  fk_id_usuario: string;
  fecha_hora_inicio_asignacion: string;
  fecha_hora_fin_asignacion?: string;
  fk_id_pedido: string;
  fk_id_estado?: string;
}

export interface IEstadisticasServicioMasSolicitado {
  descripcion_servicio: string;
  total_solicitudes: number;
  total_articulos: number;
}

export interface IEstadisticasTopCliente {
  id_cliente: number;
  nombre_cliente: string;
  apellido_cliente: string;
  identificacion_cliente: string;
  total_pedidos: number;
  total_gastado: number;
}

export interface IEstadisticasControlCaja {
  total_pedidos: number;
  ingresos_totales: number;
  subtotal: number;
  impuestos_descuentos: number;
  promedio_por_pedido: number;
}

export interface IEstadisticasGenerales {
  total_pedidos: number;
  ingresos_totales: number;
  clientes_atendidos: number;
  servicios_realizados: number;
}

export interface IEstadisticasVentaMes {
  mes: number;
  total_pedidos: number;
  ingresos_totales: number;
}