import { Component, OnDestroy } from '@angular/core';
import { FormControl, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { GlobalButtonsComponent } from '../../shared/components/global-buttons/global-buttons.component';
import { RequestService } from '../../shared/services/request.service';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { UserMessageService } from '../../shared/services/user-message.service';
import { Constantes } from '../../config/constantes';
import { Subject, takeUntil } from 'rxjs';
import { LocalStorageEncryptationService } from '../../shared/services/local-storage-encryptation.service';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RecuperacionCuentaComponent } from '../recuperacion-cuenta/recuperacion-cuenta.component';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    FormsModule,
    MatInputModule,
    MatIconModule,
    MatButtonModule,
    MatCheckboxModule,
    GlobalButtonsComponent,
    HttpClientModule,
    CommonModule,
    MatDialogModule,
        IonicModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnDestroy {

  public hidePassword: boolean = false;

  destroy$ = new Subject<void>();

  constructor(
    private http: HttpClient,
    private requesService: RequestService,
    private usermessage: UserMessageService,
    private localencript: LocalStorageEncryptationService,
    private router: Router,
    private dialog: MatDialog 
  ) { }

  changePasswordVisibility() {
    this.hidePassword = !this.hidePassword;
  }

  formlogin = new FormGroup({
    usuario: new FormControl('', [Validators.required]),
    clave: new FormControl('', [Validators.required])
  })

 public handleAction(event: string) {
  console.log(event);
  if (event == 'confirm') {
    if (this.formlogin.invalid) {
      this.usermessage.getToastMessage('info', Constantes.formInvalidMessage).fire();
      this.formlogin.markAllAsTouched();
      return;
    }
    let body = this.formlogin.getRawValue();
    this.requesService.post(body, Constantes.apiLogin)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value: any) => {
          console.log('Respuesta completa:', value); // DEBUG
          
          // VERIFICACIÓN MEJORADA
          if (!value) {
            console.error('Error: Respuesta vacía del servidor');
            this.usermessage.getToastMessage('error', 'No se recibió respuesta del servidor').fire();
            return;
          }

          // Si la respuesta es de error (respuesta: "0")
          if (value.respuesta === "0") {
            console.log('Error del servidor:', value.mensaje);
            this.usermessage.getToastMessage('error', value.mensaje || 'Error en el login').fire();
            return;
          }

          // Si es éxito, verificar que data existe
          if (!value.data) {
            console.error('Error: Estructura de respuesta inválida', value);
            this.usermessage.getToastMessage('error', 'Estructura de respuesta inválida').fire();
            return;
          }

          // Verificar si es primer inicio
          const esPrimerInicio = value.data.primer_inicio === 1 || value.data.primer_inicio === '1';
          
          // Guardar datos en localStorage
          if (value.data.token) {
            this.localencript.setLocalStorage(Constantes.tokenKey, value.data.token);
          }
          this.localencript.setLocalStorage(Constantes.usuarioKey, value.data.usuario || body.usuario);
          
          if (value.data.perfil) {
            this.localencript.setLocalStorage(Constantes.perfilKey, value.data.perfil);
          }
          if (value.data.id_usuario) {
            this.localencript.setLocalStorage(Constantes.idusuarioKey, String(value.data.id_usuario));
          }
          this.localencript.setLocalStorage('primer_inicio', esPrimerInicio ? 'true' : 'false');
          
          console.log('Login exitoso. Primer inicio:', esPrimerInicio);
          
          // Redirigir al home
          this.router.navigateByUrl('/bds/home');
        },
        error: (error) => {
          console.log('Error HTTP:', error);
          // Manejar diferentes tipos de error
          let mensajeError = 'Error de conexión';
          
          if (error.status === 0) {
            mensajeError = 'No hay conexión con el servidor';
          } else if (error.error && error.error.mensaje) {
            mensajeError = error.error.mensaje;
          } else if (error.message) {
            mensajeError = error.message;
          }
          
          this.usermessage.getToastMessage('error', mensajeError).fire();
        }
      });
  }
}

  openRecovery() {
    this.dialog.open(RecuperacionCuentaComponent, {
      width: '500px',
      disableClose: true
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
