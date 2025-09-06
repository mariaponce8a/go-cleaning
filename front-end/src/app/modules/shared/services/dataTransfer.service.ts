import { Injectable } from '@angular/core';
@Injectable({
    providedIn: 'root',
})
export class DataService {

    private datos: any;

    setDatos(nuevosDatos: any) {
        this.datos = nuevosDatos;
    }

    getDatos() {
        return this.datos;
    }

}