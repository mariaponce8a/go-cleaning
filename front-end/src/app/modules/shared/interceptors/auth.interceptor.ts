import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse,
} from '@angular/common/http';
import { Observable, catchError, throwError, timeout } from 'rxjs';
// import { Constantes } from '../constantes';
import { Router } from '@angular/router';
import { LocalStorageEncryptationService } from '../services/local-storage-encryptation.service';
import { Constantes } from '../../config/constantes';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(
    private route: Router,
    private localStorageEncryptation: LocalStorageEncryptationService
  ) { }
  defaultError: any = {
    message: Constantes.messageGeneral,
    status: 500,
  };
  intercept(
    request: HttpRequest<unknown>,
    next: HttpHandler
  ): Observable<HttpEvent<unknown>> {
    const token = this.localStorageEncryptation.getLocalStorage(
      Constantes.tokenKey
    );
    let newReq = request.clone();

    if (token) {
      newReq = request.clone({
        headers: request.headers.set('Authorization', token),
      });
    }

    return next.handle(newReq).pipe(
      timeout(Constantes.DEFAULT_TIMEOUT),
      catchError((error: HttpErrorResponse) => {
        let errorMessage: any;
        if (error.name == String('TimeoutError')) {
          return throwError(() => ({
            status: 0,
            message: 'Lo sentimos, su peticion tomó más tiempo de lo esperado.',
          }));
        }
        if (error.status === 403 || error.error.status == 403) {
          localStorage.clear();
          this.route.navigateByUrl('/autenticacion');
          errorMessage = 'Petición inválida';
        }

        if (error.error) {
          console.log(error.error)
          errorMessage = error.error;
        }
        
        console.log(errorMessage)
        return throwError(() => errorMessage);
      })
    );
  }
}
