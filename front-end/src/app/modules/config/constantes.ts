import { EnvironmentData } from './environment';

export class Constantes {
  public static TIMER_REFRESH_TOKEN: number = 600000; //milisegundos
  public static DEFAULT_TIMEOUT: number = 30000; //tiempo en milisegundos
  public static automaticLogOutKey: string = '&S3$L&O&3u^456T&'; //modals switcher key

  public static formatoFecha: string = 'yyyy-MM-dd HH:mm:ss';

  // local storage keys
  public static tokenKey: string = 'auth';
  public static usuarioKey: string = 'usuarioValue';
  public static idusuarioKey: string = 'idusuarioValue';
  public static perfilKey: string = 'perfilvalue';
  public static env = EnvironmentData.globalUrl;
  // mensajes generales
  public static messageGeneral: string =
    'Estimado cliente, intente de nuevo más tarde.';
  public static formInvalidMessage: string =
    'Por favor complete los campos correctamente.';
  public static formQuestion: string =
    '¿Está seguro que desea guardar el registro?';
  public static deleteQuestion: string =
    '¿Está seguro que desea eliminar el registro?';
  public static createResponseMsg: string =
    'Registro creado con éxito';
  public static updateResponseMsg: string =
    'Registro actualizado con éxito';
  public static deleteResponseMsg: string =
    'Registro eliminado con éxito';
  public static errorResponseMsg: string =
    'Problemas para procesar la acción, intentelo más tarde.';
  public static logOutQuestion: string =
    '¿Está seguro que desea cerrar sesión?';
  //labels generales
  public static modalHeaderMensajeEditar: string = 'Formulario edición'
  public static modalHeaderMensajeCrear: string = 'Formulario creación'

  //----------- APIS SEGURIDAD
  public static apiLogin: string = this.env.host + this.env.name + '/login';
  //----------- APIS USUARIO PLATAFORMA
  public static apiGetAllUsers: string =
    this.env.host + this.env.name + '/consultarUsuarios';
  public static apiUpdateUser: string =
    this.env.host + this.env.name + '/actualizaUsuario';
  public static apiCreateUser: string =
    this.env.host + this.env.name + '/registrarUsuario';
  public static apiDeleteUser: string =
    this.env.host + this.env.name + '/eliminarUsuario';

  //----------- APIS PEDIDOS
  public static apiGetAllPedidos: string =
    this.env.host + this.env.name + '/consultarPedidos';
  public static apiGetPedidosXId: string =
    this.env.host + this.env.name + '/consultarPedidosXid';
  public static apiPedidosNoCancelados =
    this.env.host + this.env.name + '/consultarPedidosnoCancelados';
  public static apiInsertPedido: string =
    this.env.host + this.env.name + '/registrarPedidoCompleto';
  public static apiOrdenPedido: string =
    this.env.host + this.env.name + '/ordenPedido';
  public static apiGetPedidosNoFinalizados: string =
    this.env.host + this.env.name + '/consultarPedidosNoFinalizados';
  public static ejecutarFacturacion: string =
    this.env.host + this.env.name + '/ejecutarfacturacion'


  //----------- APIS  SERVICIOS
  public static apiGetAllServices: string =
    this.env.host + this.env.name + '/consultarServicios';
  public static apiUpdateServices: string =
    this.env.host + this.env.name + '/actualizarServicios';
  public static apiCreateServices: string =
    this.env.host + this.env.name + '/registrarServicios';
  public static apiDeleteServices: string =
    this.env.host + this.env.name + '/eliminarServicio';

  //----------- APIS  clientes
  public static apiGetAllClientes: string =
    this.env.host + this.env.name + '/consultarClientes';
  public static apiUpdateCliente: string =
    this.env.host + this.env.name + '/actualizarCliente';
  public static apiCreateCliente: string =
    this.env.host + this.env.name + '/registrarCliente';

  public static apiDeleteCliente: string =
    this.env.host + this.env.name + '/eliminarCliente';

  //----------- APIS  Descuentos
  public static apiGetAllDescuentos: string =
    this.env.host + this.env.name + '/consultarDescuentos';
  public static apiUpdateDescuentos: string =
    this.env.host + this.env.name + '/actualizarDescuentos';
  public static apiCreateDescuentos: string =
    this.env.host + this.env.name + '/registrarDescuentos';
  public static apiDeleteDescuentos: string =
    this.env.host + this.env.name + '/eliminarDescuentos';

  //----------- APIS  Materiales
  public static apiGetAllMaterials: string =
    this.env.host + this.env.name + '/consultarMateriales';
  public static apiUpdateMaterial: string =
    this.env.host + this.env.name + '/editarMaterial';
  public static apiCreateMaterial: string =
    this.env.host + this.env.name + '/registrarMaterial';
  public static apiDeleteMaterial: string =
    this.env.host + this.env.name + '/eliminarMaterial';
  public static apiGetMaterial: string =
    this.env.host + this.env.name + '/consultarMaterial';


  //----------- APIS  Estados 
  public static apiGetAllEstados: string =
    this.env.host + this.env.name + '/consultarEstados';
  public static apiUpdateEstados: string =
    this.env.host + this.env.name + '/actualizarEstado';
  public static apiCreateEstados: string =
    this.env.host + this.env.name + '/registrarEstado';
  public static apiDeleteEstados: string =
    this.env.host + this.env.name + '/eliminarEstado';

  //----------- APIS  Recomendaciones 
  public static apiGetAllRecommendations: string =
    this.env.host + this.env.name + '/consultarRecomendaciones';
  public static apiUpdateRecomendaciones: string =
    this.env.host + this.env.name + '/actualizarRecomendacion';
  public static apiCreateRecomendaciones: string =
    this.env.host + this.env.name + '/registrarRecomendacion';
  public static apiDeleteRecomendaciones: string =
    this.env.host + this.env.name + '/eliminarRecomendacion';

  //----------- APIS  asignaciones
  public static apiGetAllAsignaciones: string =
    this.env.host + this.env.name + '/consultarAsignaciones';
  public static apiUpdateAsignaciones: string =
    this.env.host + this.env.name + '/actualizarAsignacion';
  public static apiCreateAsignaciones: string =
    this.env.host + this.env.name + '/registrarAsignacion';
  public static apiDeleteAsignaciones: string =
    this.env.host + this.env.name + '/eliminarAsignacion';

  //----------- API MENSAJE
  public static apiSendMessage: string =
    this.env.host + this.env.name + '/enviarMensaje';




}
