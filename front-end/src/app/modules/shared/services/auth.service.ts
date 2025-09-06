import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, catchError, finalize, map, of, tap } from 'rxjs';
import { Constantes } from '../../config/constantes';
import { LocalStorageEncryptationService } from './local-storage-encryptation.service';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  constructor(
    private router: Router,
    private http: HttpClient,
    private localStorageEncryptation: LocalStorageEncryptationService
  ) { }
  
  logout(): void {
    localStorage.removeItem('token');
    this.router.navigateByUrl("/autenticacion");
    this.removeAll();
  }

  setLocalStorageAutomaticLogout(reason: string) {
    this.localStorageEncryptation.setLocalStorage(
      Constantes.automaticLogOutKey,
      reason
    );
  }
  removeAll(): void {
    const keysToRemove = [
      'auth',
      'idusuarioValue',
      'loglevel',
      'perfilvalue',
      'usuarioValue'
    ];
  
    keysToRemove.forEach(key => localStorage.removeItem(key));
  }

  get tokenAuth() {
    return this.localStorageEncryptation.getLocalStorage(
      Constantes.tokenKey
    );
  }


  isAuthenticated(): boolean {
    const token = this.tokenAuth;
    return token !== null && token !== '';
  }


    
  refreshToken(): Observable<boolean> {
    return this.http
      .put<any>(`refreshseseion`, {})
      .pipe(
        tap((data: any) => {
          if (data.code === '0000') {
            this.localStorageEncryptation.setLocalStorage(
              Constantes.tokenKey,
              data.data?.token ? data.data?.token : ''
            );
          }
        }),
        map((resp: any) => {
          return true;
        }),
        catchError((error) => of(false))
      );
  }

  validateSesion(token: string): Observable<any> {
    return this.http.post<any>(`${Constantes.tokenKey}/v1/authentication/validate`, { token })
      .pipe(
        catchError(() => of({ isAuthenticated: false })) // Manejar errores retornando un objeto por defecto
      );
  }
  }

