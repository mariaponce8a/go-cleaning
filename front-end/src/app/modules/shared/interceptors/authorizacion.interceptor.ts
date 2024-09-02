import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { LocalStorageEncryptationService } from '../services/local-storage-encryptation.service';
import { Constantes } from '../../config/constantes';
import { catchError, throwError } from 'rxjs';
import { Router } from '@angular/router';

export const authorizacionInterceptor: HttpInterceptorFn = (req, next) => {

  const lcEncriptServ = inject(LocalStorageEncryptationService);
  const router = inject(Router);

  const authToken = lcEncriptServ.getLocalStorage(Constantes.tokenKey);
  const authReq = req.clone({
    headers: req.headers.set('Authorization', authToken ?? '')
  });

  return next(authReq)
    .pipe(
      catchError((err: any) => {
        let errorMessage: any;
        if (err instanceof HttpErrorResponse) {

          if (err.name == String('TimeoutError')) {
            return throwError(() => ({
              status: 0,
              message: 'Lo sentimos, su peticion tomó más tiempo de lo esperado.',
            }));
          }
          if (err.status === 403) {
            localStorage.clear();
            router.navigateByUrl('/login');
            errorMessage = 'Petición no autorizada';
          }
        }

        return throwError(() => errorMessage);
      })
    );
};
