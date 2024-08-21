export class Constantes {

    public static TIMER_REFRESH_TOKEN: number = 600000; //milisegundos
    public static DEFAULT_TIMEOUT: number = 30000; //tiempo en milisegundos
    public static automaticLogOutKey: string = '&S3$L&O&3u^456T&'; //modals switcher key

    // local storage keys
    public static tokenKey: string = 'auth';
    public static usuarioKey: string = 'usuarioValue';
    public static perfilKey: string = 'perfilvalue';
    // mensajes generales
    public static messageGeneral: string = 'Estimado cliente, intente de nuevo más tarde.'
    public static formInvalidMessage: string = 'Por favor complete los campos correctamente.';

    // APIS USUARIO PLATAFORMA
    public static apiLogin: string = 'http://localhost/proyecto-integrador-burbujas-seda/login';
}