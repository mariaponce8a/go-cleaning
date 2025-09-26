import { Component, OnInit, OnDestroy } from '@angular/core';
import { FormControl, FormGroup, Validators, AbstractControl, ValidationErrors } from '@angular/forms';
import { Router } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { CommonModule } from '@angular/common';
import { MaterialModule } from '../../desginModules/material.module';
import { IonicModule } from '@ionic/angular';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';

import { ColoredBodyHeaderComponent } from "../../shared/components/colored-body-header/colored-body-header.component";
import { LocalStorageEncryptationService } from '../../shared/services/local-storage-encryptation.service';
import { UserMessageService } from '../../shared/services/user-message.service';
import { RequestService } from '../../shared/services/request.service';
import { Constantes } from '../../config/constantes';

export interface IUsuarioPerfil {
  id_usuario: any;
  usuario: string;
  nombre: string;
  apellido: string;
  perfil: string;
}

@Component({
  selector: 'app-perfil',
  standalone: true,
  imports: [
    CommonModule,
    MaterialModule,
    IonicModule,
    MatDialogModule,
    MatIconModule,
    MatCardModule,
    ColoredBodyHeaderComponent
  ],
  templateUrl: './perfil.component.html',
  styleUrl: './perfil.component.css'
})
export class PerfilComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  loading = false;
  isEditMode = false;
  loadingUpdate: boolean = false;
  mostrarCambioPassword = false;
  mostrarVerificacion = false; 

  idUsuario?: string | null;
  perfilPersona?: string | null;
  perfilPersonaDesc = '';
  usuarioData?: IUsuarioPerfil;
  originalData?: IUsuarioPerfil;

  // Control de visibilidad de contraseñas
  hideActual = true;
  hideNueva = true;
  hideConfirmar = true;

  passwordStrength: string = '';

  // ✅ SEPARAR FORMULARIOS - Formulario principal para datos del perfil
  perfilForm = new FormGroup({
    id_usuario: new FormControl(''),
    usuario: new FormControl({ value: '', disabled: true }, [Validators.required]),
    nombre: new FormControl('', [Validators.required, Validators.minLength(2)]),
    apellido: new FormControl(''),
    perfil: new FormControl({ value: '', disabled: true })
  });

  // ✅ FORMULARIO SEPARADO para cambio de contraseña
  passwordForm = new FormGroup({
    claveActual: new FormControl('', [Validators.required, Validators.minLength(6)]),
    claveNueva: new FormControl('', [Validators.required, Validators.minLength(6)]),
    confirmarClave: new FormControl('', [Validators.required])
  }, { validators: this.passwordMatchValidator });

  // ✅ FORMULARIO SEPARADO para verificación de edición
  verificationForm = new FormGroup({
    claveActual: new FormControl('', [Validators.required, Validators.minLength(6)])
  });

  // Validador para coincidencia de contraseñas
  private passwordMatchValidator(control: AbstractControl): ValidationErrors | null {
    const claveNueva = control.get('claveNueva')?.value;
    const confirmarClave = control.get('confirmarClave')?.value;
    return claveNueva === confirmarClave ? null : { mismatch: true };
  }

  constructor(
    private requestService: RequestService,
    private usermessage: UserMessageService,
    private localStorage: LocalStorageEncryptationService,
    private router: Router
  ) { }

  ngOnInit(): void {
    // Listener para fortaleza de contraseña
    this.passwordForm.get('claveNueva')?.valueChanges.subscribe(value => {
      this.evaluarFortalezaPassword(value ?? '');
    });

    this.idUsuario = this.localStorage.getLocalStorage(Constantes.idusuarioKey);
    this.perfilPersona = this.localStorage.getLocalStorage(Constantes.perfilKey);

    if (!this.idUsuario) {
      this.usermessage.getToastMessage('error', 'No se encontró el ID del usuario').fire();
      return;
    }

    if (this.perfilPersona === 'E') {
      this.perfilPersonaDesc = 'Empleado';
    } else if (this.perfilPersona === 'A') {
      this.perfilPersonaDesc = 'Administrador';
    } else {
      this.perfilPersonaDesc = 'Usuario';
    }

    this.cargarDatos();
  }

  cargarDatos(): void {
    if (!this.idUsuario) { 
      this.usermessage.getToastMessage('error', 'No se encontró el ID del usuario').fire();
      this.loading = false;
      return;
    }

    this.loading = true;
    this.requestService.get(`${Constantes.apiGetUserbyId}/${this.idUsuario}`)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (resp) => {
          this.loading = false;
          console.log('Respuesta perfil:', resp);
          if (resp.respuesta === '1' && resp.data) {
            this.usuarioData = resp.data;
            this.originalData = { ...resp.data }; // Guardar copia para cancelar
            this.actualizarFormulario(resp.data);
          } else {
            this.usermessage.getToastMessage('error', resp.mensaje || Constantes.messageGeneral).fire();
          }
        },
        error: (err) => {
          this.loading = false;
          console.error('Error cargar perfil:', err);
          this.usermessage.getToastMessage('error', Constantes.messageGeneral).fire();
        }
      });
  }

  actualizarFormulario(usuario: IUsuarioPerfil) {
    if (!usuario) {
      console.warn('Usuario data es null');
      return;
    }

    if (this.perfilPersona === 'E') {
      this.perfilPersonaDesc = 'Empleado';
    } else if (this.perfilPersona === 'A') {
      this.perfilPersonaDesc = 'Administrador';
    } else {
      this.perfilPersonaDesc = usuario.perfil || 'Usuario';
    }

    this.perfilForm.patchValue({
      id_usuario: usuario.id_usuario,
      usuario: usuario.usuario,
      nombre: usuario.nombre,
      apellido: usuario.apellido,
      perfil: this.perfilPersonaDesc
    });
  }

  toggleEdit(): void {
    this.isEditMode = !this.isEditMode;
    
    if (this.isEditMode) {
      // Habilitar edición
      this.perfilForm.controls.nombre.enable();
      this.perfilForm.controls.apellido.enable();
      this.perfilForm.controls.usuario.enable();
      this.mostrarVerificacion = false;
      this.verificationForm.reset(); // Limpiar formulario de verificación
    } else {
      // Cancelar edición - restaurar datos originales
      if (this.originalData) {
        this.actualizarFormulario(this.originalData);
      }
      this.perfilForm.controls.nombre.disable();
      this.perfilForm.controls.apellido.disable();
      this.perfilForm.controls.usuario.disable();
      this.mostrarVerificacion = false;
      this.verificationForm.reset();
    }
  }

  guardarCambios(): void {
    // Primera vez: mostrar verificación
    if (!this.mostrarVerificacion) {
      this.mostrarVerificacion = true;
      return;
    }

    // Segunda vez: validar y enviar
    if (this.verificationForm.invalid) {
      this.usermessage.getToastMessage('info', 'Ingresa tu contraseña actual para confirmar los cambios');
      return;
    }

    if (this.perfilForm.invalid || !this.idUsuario) {
      this.usermessage.getToastMessage('info', Constantes.formInvalidMessage);
      return;
    }

    const body = {
      id_usuario: Number(this.idUsuario),
      nombre: this.perfilForm.value.nombre,
      apellido: this.perfilForm.value.apellido,
      usuario: this.perfilForm.getRawValue().usuario ?? '',
      clave_actual: this.verificationForm.value.claveActual // ✅ Usar verificationForm
    };

    this.loadingUpdate = true;

    this.usermessage.questionMessage(Constantes.formQuestion).then(r => {
      if (r.isConfirmed) {
        this.requestService.put(body, Constantes.apiUpdateUser)
          .pipe(takeUntil(this.destroy$))
          .subscribe({
            next: (resp) => {
              this.loadingUpdate = false;
              if (resp.respuesta === '1') {
                this.usermessage.getToastMessage('success', resp.mensaje || Constantes.updateResponseMsg).fire();
                
                // Actualizar datos locales
                this.originalData = {
                  id_usuario: this.perfilForm.getRawValue().id_usuario,
                  usuario: this.perfilForm.getRawValue().usuario ?? '',
                  nombre: this.perfilForm.getRawValue().nombre ?? '',
                  apellido: this.perfilForm.getRawValue().apellido ?? '',
                  perfil: this.perfilForm.getRawValue().perfil ?? ''
                };
                
                this.mostrarVerificacion = false;
                this.verificationForm.reset();
                this.toggleEdit(); // Salir del modo edición
                this.cargarDatos(); // Recargar datos
              } else {
                this.usermessage.getToastMessage('error', resp.mensaje || Constantes.errorResponseMsg).fire();
              }
            },
            error: (err) => {
              this.loadingUpdate = false;
              console.error('Error actualizar perfil:', err);
              this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
            }
          });
      } else {
        this.loadingUpdate = false;
      }
    });
  }

  cambiarPassword(): void {
    if (this.passwordForm.invalid || !this.idUsuario) {
      this.usermessage.getToastMessage('info', Constantes.formInvalidMessage).fire();
      return;
    }

    const body = {
      id_usuario: Number(this.idUsuario),
      clave_actual: this.passwordForm.value.claveActual,
      clave_nueva: this.passwordForm.value.claveNueva,
      confirmar_clave: this.passwordForm.value.confirmarClave
    };

    this.loading = true;

    this.usermessage.questionMessage(Constantes.changePasswordQuestion).then(r => {
      if (r.isConfirmed) {
        this.requestService.put(body, Constantes.apiChangePassword)
          .pipe(takeUntil(this.destroy$))
          .subscribe({
            next: (resp) => {
              this.loading = false;
              if (resp.respuesta === '1') {
                this.usermessage.getToastMessage('success', Constantes.changePasswordMsg).fire();
                this.passwordForm.reset();
                this.mostrarCambioPassword = false;
              } else {
                this.usermessage.getToastMessage('error', resp.mensaje || Constantes.errorResponseMsg).fire();
              }
            },
            error: (err) => {
              this.loading = false;
              console.error('Error cambiar password:', err);
              this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
            }
          });
      } else {
        this.loading = false;
      }
    });
  }

  // Métodos de fortaleza de contraseña (sin cambios)
  evaluarFortalezaPassword(password: string): void {
    if (!password) {
      this.passwordStrength = '';
      return;
    }

    let strength = 0;
    const length = password.length;

    if (length >= 8) strength++;
    if (length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    if (strength <= 2) {
      this.passwordStrength = 'Débil';
    } else if (strength <= 4) {
      this.passwordStrength = 'Media';
    } else {
      this.passwordStrength = 'Fuerte';
    }
  }

  getPasswordStrengthClass(): string {
    switch (this.passwordStrength) {
      case 'Débil':
        return 'password-weak';
      case 'Media':
        return 'password-medium';
      case 'Fuerte':
        return 'password-strong';
      default:
        return '';
    }
  }

  volver(): void {
    this.router.navigate(['/bds/home']);
  }

  getCurrentTime(): string {
    return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}