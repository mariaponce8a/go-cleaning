import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IusuariosPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-form-usuarios',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent,
    CommonModule,
    IonicModule,
    ReactiveFormsModule 
  ],
  templateUrl: './form-usuarios.component.html',
  styleUrl: './form-usuarios.component.css'
})
export class FormUsuariosComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormUsuariosComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { }
  
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;
  esActualizacionPerfil: boolean = false;

  ngOnInit(): void {
    console.log(this.data);
    
    // Determinar el tipo de acción
    this.esActualizacionPerfil = this.data.tipo === 'editar';
     if (this.esActualizacionPerfil) {
      this.tituloPorAccion = 'Actualizar Perfil';
      this.configurarFormularioActualizacionPerfil();
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
      this.configurarFormularioCreacion();
    }
  }

  // Configurar formulario para creación
  configurarFormularioCreacion(): void {
    this.form.controls.usuario.enable();
    this.form.controls.nombre.enable();
    this.form.controls.apellido.enable();
    this.form.controls.email.enable();
    this.form.controls.perfil.enable();

    this.form.reset();

  }

 // Configurar formulario para actualización de perfil
configurarFormularioActualizacionPerfil(): void {
  const datos = { ...this.data.fila };
  
  // Convertir el perfil de 'A'/'E' a texto para el formulario
  if (datos.perfil === 'A') {
    datos.perfil = 'Administrador';
  } else if (datos.perfil === 'E') {
    datos.perfil = 'Empleado';
  }
  
  this.form.patchValue(datos);
  
  // Solo el campo perfil es editable en actualización de perfil
  this.form.controls.usuario.disable();
  this.form.controls.nombre.disable();
  this.form.controls.apellido.disable();
  this.form.controls.email.disable();
  this.form.controls.perfil.enable();
}

  form = new FormGroup({
    id_usuario: new FormControl(''),
    usuario: new FormControl({ value: '', disabled: false }, [Validators.required]),
    nombre: new FormControl('', [Validators.required]),
    apellido: new FormControl('', [Validators.required]),
    email: new FormControl('', [Validators.required, Validators.email]), 
    perfil: new FormControl('', [Validators.required]),
  })

  cerrarModalSinInformacion(cerrar: boolean) {
    if (cerrar) {
      this.dialogRef.close();
    }
  }

  cerrarModalConInformacion() {
    this.dialogRef.close('ok');
  }

  // Método específico para actualizar perfil
  actualizarPerfilUsuario(body: any) {
    const perfilBody = {
      id_usuario: body.id_usuario,
      nuevo_perfil: body.perfil
    };

    this.requestservice.put(perfilBody, Constantes.apiUpdateProfile)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value: any) => {
          if (value.respuesta === '1') {
            this.usermessage.getToastMessage('success', 'Perfil actualizado correctamente').fire();
            this.cerrarModalConInformacion();
          } else {
            this.usermessage.getToastMessage('error', value.mensaje || Constantes.errorResponseMsg).fire();
          }
        },
        error: (error) => {
          console.error('Error actualizando perfil:', error);
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      });
  }

  crearUsuario(body: any) {
    this.requestservice.post(body, Constantes.apiCreateUser)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value: any) => {
          if (value.respuesta === '1') {
            this.usermessage.getToastMessage('success', Constantes.temporaryPasswordMsg).fire();
            this.cerrarModalConInformacion();
          } else {
            this.usermessage.getToastMessage('error', value.mensaje || Constantes.errorResponseMsg).fire();
          }
        },
        error: (error) => {
          console.error('Error creando usuario:', error);
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      })
  }

  guardar() {
    if (this.form.invalid) {
      this.usermessage.getToastMessage('info', Constantes.formInvalidMessage).fire();
      this.form.markAllAsTouched();
      return;
    }

    let body = this.form.getRawValue();
    
    // Convertir perfil a formato de backend
    if (body.perfil == 'Empleado') {
      body.perfil = 'E';
    } else {
      body.perfil = 'A';
    }

    this.usermessage.questionMessage(Constantes.formQuestion).then((r) => {
      if (r.isConfirmed) {
        if (this.esActualizacionPerfil) {
          this.actualizarPerfilUsuario(body);
        } else {
          this.crearUsuario(body);
        }
      }
    })
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}