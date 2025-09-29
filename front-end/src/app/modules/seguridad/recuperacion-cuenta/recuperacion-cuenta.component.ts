import { Component, OnDestroy } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
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
import { MatDialogRef, MatDialogModule } from '@angular/material/dialog';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner'; // Añade esto

@Component({
  selector: 'app-recuperacion-cuenta',
  standalone: true,
  imports: [
    MatInputModule,
    MatIconModule,
    MatButtonModule,
    FormsModule,
    ReactiveFormsModule,
    MatCheckboxModule,
    MatProgressSpinnerModule, 
    GlobalButtonsComponent,
    HttpClientModule,
    CommonModule,
    MatDialogModule,
        IonicModule
  ],
  templateUrl: './recuperacion-cuenta.component.html',
  styleUrl: './recuperacion-cuenta.component.css'
})
export class RecuperacionCuentaComponent {
  formRecuperacion: FormGroup;
  isLoading: boolean = false;
  errorMessage: string = '';
  successMessage: string = '';
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private dialogRef: MatDialogRef<RecuperacionCuentaComponent>
  ) {
    this.formRecuperacion = this.fb.group({
      nombre: ['', Validators.required],
      apellido: ['', Validators.required],
      usuario: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]]
    });
  }
onSubmit() {
    // Prevenir múltiples envíos
    if (this.formRecuperacion.invalid || this.isSubmitting) {
        return;
    }

    this.isSubmitting = true;
    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';

    this.http.post(`${Constantes.apiRequestPasswordReset}`, this.formRecuperacion.value)
        .subscribe({
            next: (response: any) => {
                this.isLoading = false;
                this.isSubmitting = false;
                
                if (response.respuesta === "1") { // Cambiado a tu formato de respuesta
                    this.successMessage = response.mensaje || 'Se ha enviado una clave temporal a tu correo. Tienes 15 minutos para cambiarla.';
                    
                    // Deshabilitar el formulario después del éxito
                    this.formRecuperacion.disable();
                    
                    // Cerrar automáticamente después de 3 segundos
                    setTimeout(() => {
                        this.dialogRef.close(true); // Puedes pasar un valor si necesitas
                    }, 3000);
                } else {
                    this.errorMessage = response.mensaje || 'Error al procesar la solicitud';
                }
            },
            error: (error) => {
                this.isLoading = false;
                this.isSubmitting = false;
                this.errorMessage = error.error?.mensaje || 'Error de conexión';
            }
        });
}

  onCancel() {
    this.dialogRef.close();
  }
}
