import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import Swal, { SweetAlertIcon } from "sweetalert2";

@Injectable({
  providedIn: 'root'
})
export class UserMessageService {

  constructor(
    public http: HttpClient,
  ) { }


  showMessage(message: string, icon?: SweetAlertIcon, title?: string) {

    return Swal.fire({
      title: title ? title : '',
      text: message,
      icon: icon ? icon : 'info',
      confirmButtonText: 'Ok',
      confirmButtonColor: '#0061f2',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false
    });
  }

  getToastMessage(icon: SweetAlertIcon, text: string) {
    return Swal.mixin({
      icon: icon ?? 'info',
      text: text ?? 'agregar mensaje en el parametro',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });
  }

  showTimeoutMessage() {
    return Swal.fire({
      showConfirmButton: false,
      title: 'Tu sesión a expirado por inactividad',
      text: 'Te llevaremos de nuevo al inicio de sesión',
      timer: 5000,
      timerProgressBar: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false
    });
  }

  showWarningMessageWithTimer(message: string, title?: string) {
    return Swal.fire({
      showConfirmButton: false,
      icon: 'warning',
      title: title,
      text: message,
      timer: 10000,
      timerProgressBar: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false
    });
  }

  questionMessage(message: string, title?: string) {
    return Swal.fire({
      title: title ? title : '',
      text: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'SÍ',
      cancelButtonText: 'NO',
      confirmButtonColor: '#0d6efd',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false
    });
  }

}
