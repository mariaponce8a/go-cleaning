import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Constantes } from '../../config/constantes'; // Ajusta ruta

@Injectable({
  providedIn: 'root'
})
export class CambioClaveService {
  private apiUrl = Constantes.apiSetInitialPassword || '/cambiar-clave'; // Usa tu constante

  constructor(private http: HttpClient) {}

  cambiarClaveInicial(idUsuario: string, claveTemporal: string, nuevaClave: string, confirmarClave: string, token: string): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });

    const body = {
      id_usuario: idUsuario,
      clave_temporal: claveTemporal,
      nueva_clave: nuevaClave,
      confirmar_clave: confirmarClave
    };

    return this.http.put(this.apiUrl, body, { headers });
  }
}