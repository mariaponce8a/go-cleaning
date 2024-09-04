export interface ITitulosTabla {
  value: string; //valor visible
  viewValue: string; //valor de referencia
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
  id_usuario: string | number;
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

export interface IserviciosPlataforma {
  id_servicio: number;
  descripcion_servicio: string;
  costo_unitario?: number;
  validar_pesaje?: any; // llega un numero pero se lo reemplaza por un string
}

export interface IclientesPlataforma {
  id_cliente: number;
  identificacion_cliente: string;
  tipo_identificacion_cliente: string;
  nombre_cliente: string;
  apellido_cliente: string;
  telefono_cliente: string;
  correo_cliente: string;
}
export interface IdescuentosPlataforma {
  id_tipo_descuento: number;
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
  id_asignaciones: number;
  fk_id_usuario: number;
  fecha_hora_inicio_asignacion: string;
  fecha_hora_fin_asignacion?: string;
  fk_id_pedido: number;
  fk_id_estado?: number;
}

