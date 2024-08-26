export interface ITitulosTabla {
    value: string, //valor visible
    viewValue: string //valor de referencia
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