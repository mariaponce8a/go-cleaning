export interface ITitulosTabla {
  value: string; //valor visible
  viewValue: string; //valor de referencia
}

export interface IMenu {
  icon: string;
  pagina: string;
  descripcion: string;
  perfil: string;
}

export interface IusuariosPlataforma {
  id_usuario: number;
  usuario: string;
  nombre: string;
  apellido: string;
  perfil: string;
  clave: string;
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
