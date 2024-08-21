import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, catchError, finalize, map, of, tap } from 'rxjs';
import { Constantes } from '../../config/constantes';
import { LocalStorageEncryptationService } from './local-storage-encryptation.service';
// import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  constructor(
    private router: Router,
    private http: HttpClient,
    private localStorageEncryptation: LocalStorageEncryptationService
  ) { }

  // logout(reason: string): Observable<any> {
  //   return this.http
  //     .post<any>(`${environment.urlAuth}/v1/authentication/logout`, {})
  //     .pipe(
  //       finalize(() => {
  //         this.router.navigateByUrl("/autenticacion");
  //         this.removeAll();
  //         this.setLocalStorageAutomaticLogout(reason);
  //       })
  //     );
  // }

  setLocalStorageAutomaticLogout(reason: string) {
    this.localStorageEncryptation.setLocalStorage(
      Constantes.automaticLogOutKey,
      reason
    );
  }
  removeAll() {

  }

  get tokenAuth() {
    return this.localStorageEncryptation.getLocalStorage(
      Constantes.tokenKey
    );
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

  validateSesion(): Observable<any> {
    return this.http.post<any>(
      `url`,
      {}
    );
  }
}
