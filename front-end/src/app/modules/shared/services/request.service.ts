import { Injectable } from '@angular/core';
import {
  HttpClient, 
  HttpHeaders,
  HttpParams,
} from '@angular/common/http';
import { catchError, Observable, throwError } from 'rxjs';
// import { IResponse } from '../interfaces/IResponse';
import { Constantes } from '../../config/constantes';
// import { RequestException } from '../exceptions/request.exception';
import { AuthService } from './auth.service';
import { LocalStorageEncryptationService } from './local-storage-encryptation.service';

@Injectable({
  providedIn: 'root',
})
export class RequestService {

  constructor(
    public http: HttpClient,
    public auth: AuthService,
    private localStorageEncryptation: LocalStorageEncryptationService
  ) { }

  post(request: any, context: string): Observable<any> {
    return this.http
      .post<any>(context, request)
      .pipe();
  }


  delete(context: string): Observable<any> {
    return this.http
      .delete<any>(context, this.httpHeaders())
      .pipe();
  }

  put(
    request: any,
    context: string,
    queryParams?: Map<string, any>
  ): Observable<any> {
    if (queryParams) {
      let params = new HttpParams();
      for (let [key, value] of queryParams) {
        params = params.set(key, value);
      }
      context = context.concat('?', params.toString());
    }

    return this.http
      .put<any>(context, request)
      .pipe();
  }

  get(context: any, queryParams?: Map<string, any>): Observable<any> {
    if (queryParams) {
      let params = new HttpParams();
      for (let [key, value] of queryParams) {
        params = params.set(key, value);
      }
      context = context.concat('?', params.toString());
    }

    return this.http
      .get<any>(context, this.httpHeaders(true))
      .pipe();
  }

  private httpHeaders(isGet: boolean = false) {
    let httpOptions = {
      Accept: 'application/json',
    };

    if (this.localStorageEncryptation.getLocalStorage(Constantes.tokenKey) !== null) {
      const pair = {
        Authorization: this.localStorageEncryptation.getLocalStorage(
          Constantes.tokenKey
        ),
      };
      httpOptions = { ...httpOptions, ...pair };
    }

    if (!isGet) {
      const pair = { 'Content-type': 'application/json', 'Access-Control-Allow-Origin': '*' };
      httpOptions = { ...httpOptions, ...pair };
    }

    return {
      headers: new HttpHeaders(httpOptions),
    };
  }


}
