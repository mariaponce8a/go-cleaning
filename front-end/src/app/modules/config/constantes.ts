import { EnvironmentData } from './environment';

export class Constantes {
  public static TIMER_REFRESH_TOKEN: number = 600000; //milisegundos
  public static DEFAULT_TIMEOUT: number = 30000; //tiempo en milisegundos
  public static automaticLogOutKey: string = '&S3$L&O&3u^456T&'; //modals switcher key

  // local storage keys
  public static tokenKey: string = 'auth';
  public static usuarioKey: string = 'usuarioValue';
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
    'Porblemas para procesar la acción, intentelo más tarde.';
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


    
  //----------- APIS  SERVICIOS
  public static apiGetAllServices: string =
    this.env.host + this.env.name + '/consultarServicios';

  //----------- APIS  clientes
  public static apiGetAllClientes: string =
    this.env.host + this.env.name + '/consultarClientes';

  //----------- APIS  Descuentos
  public static apiGetAllDescuentos: string =
    this.env.host + this.env.name + '/consultarDescuentos';

  //----------- APIS  Materiales
  public static apiGetAllMaterials: string =
    this.env.host + this.env.name + '/consultarMateriales';
    public static apiUpdateMaterial: string =
    this.env.host + this.env.name + '/editarMaterial';
  public static apiCreateMaterial: string =
    this.env.host + this.env.name + '/registrarMaterial';
  public static apiDeleteMaterial: string =
    this.env.host + this.env.name + '/eliminarMaterial';


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
    this.env.host + this.env.name + '/consultarRecomendaciones';






}
