import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
import { FormBuilder, FormGroup, Validators, AbstractControl } from '@angular/forms';
import { Subject, takeUntil } from 'rxjs';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { MaterialModule } from '../../desginModules/material.module';
import { IonicModule } from '@ionic/angular';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { CambioClaveService } from '../../shared/services/cambio-clave.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Router } from '@angular/router'; // Nuevo: para Router

import { ColoredBodyHeaderComponent } from "../../shared/components/colored-body-header/colored-body-header.component";
import { LocalStorageEncryptationService } from '../../shared/services/local-storage-encryptation.service';
import { UserMessageService } from '../../shared/services/user-message.service';
import { Constantes } from '../../config/constantes';

@Component({
  selector: 'app-cambiar-password-obligatorio',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MaterialModule,
    IonicModule,
    MatDialogModule,
    MatIconModule,
    MatCardModule,
    ColoredBodyHeaderComponent
  ],
  templateUrl: './cambiar-password-obligatorio.component.html',
  styleUrl: './cambiar-password-obligatorio.component.scss' // Usa .scss si lo tienes
})
export class CambiarPasswordObligatorioComponent implements OnInit, OnDestroy {
  formCambioClave: FormGroup;
  isLoading = false;
  errorMensaje = '';
  clavesNoCoinciden = false; // Propiedad pública (accesible en template)
  private destroy$ = new Subject<void>();

  constructor(
    private fb: FormBuilder,
    private cambioClaveService: CambioClaveService,
    private localencript: LocalStorageEncryptationService,
    private usermessage: UserMessageService,
    private router: Router,
    public dialogRef: MatDialogRef<CambiarPasswordObligatorioComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { token: string; idUsuario: string; claveTemporal?: string } // Opcional: claveTemporal pre-llenada
  ) {
    this.formCambioClave = this.fb.group({
      claveTemporal: ['', [Validators.required, Validators.minLength(6)]], // Nuevo campo para modelo PHP
      nuevaClave: ['', [Validators.required, Validators.minLength(6)]], // Min 6 como en tu modelo
      confirmarClave: ['', Validators.required]
    });

    // Si claveTemporal viene en data (ej. de localStorage o login), pre-llenarla
    if (this.data.claveTemporal) {
      this.formCambioClave.patchValue({ claveTemporal: this.data.claveTemporal });
    }
  }

  ngOnInit() {
    this.formCambioClave.get('confirmarClave')?.valueChanges
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => {
        this.validarCoincidencia();
      });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  validarCoincidencia() {
    const nueva = this.formCambioClave.get('nuevaClave')?.value;
    const confirmar = this.formCambioClave.get('confirmarClave')?.value;
    this.clavesNoCoinciden = !!(nueva && confirmar && nueva !== confirmar); // Actualiza la propiedad
  }

  onSubmit() {
    if (this.formCambioClave.invalid || this.clavesNoCoinciden) {
      this.formCambioClave.markAllAsTouched();
      this.errorMensaje = 'Por favor, corrige los errores en el formulario (verifica coincidencia y longitud).';
      return;
    }

    this.isLoading = true;
    this.errorMensaje = '';

    // Obtener valores del form
    const claveTemporal = this.formCambioClave.get('claveTemporal')?.value;
    const nuevaClave = this.formCambioClave.get('nuevaClave')?.value;
    const confirmarClave = this.formCambioClave.get('confirmarClave')?.value;
    const idUsuario = this.data.idUsuario;
    const token = this.data.token;

    // Llamar al servicio con los 4 params requeridos por tu modelo PHP
    this.cambioClaveService.cambiarClaveInicial(idUsuario, claveTemporal, nuevaClave, confirmarClave, token)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          // Manejar respuesta: Prioridad a "respuesta" del controlador, fallback a "success" del modelo
          const esExito = (response.respuesta === '1') || (response.success === true);
          if (esExito) {
            // Éxito: Actualizar localStorage y cerrar modal
            this.localencript.setLocalStorage(Constantes.tokenKey, response.data?.token || token);
            this.localencript.setLocalStorage('primer_inicio', 'false');
            
            // Opcional: Mostrar toast de éxito
            this.usermessage.getToastMessage('success', response.mensaje || response.message || 'Clave cambiada exitosamente').fire();
            
            this.dialogRef.close({ success: true, nuevoToken: response.data?.token });
          } else {
            // Error del backend (ej. temporal expirada, no coinciden, etc.)
            this.errorMensaje = response.mensaje || response.message || 'Error al cambiar la clave. Verifica la contraseña temporal.';
          }
          this.isLoading = false;
        },
        error: (error) => {
          console.error('Error en cambio de clave:', error);
          this.errorMensaje = 'Error de conexión con el servidor. Intenta nuevamente.';
          this.isLoading = false;
        }
      });
  }

  onCancel() {
    // Cierra el modal sin éxito (puedes redirigir a login si quieres forzar el cambio)
    this.dialogRef.close({ success: false });
    this.router.navigateByUrl('/login'); // Descomenta para forzar logout
  }


}