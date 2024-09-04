import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IAsignacionEmpleadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';

@Component({
  selector: 'app-form-asignaciones',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-asignaciones.component.html',
  styleUrl: './form-asignaciones.component.css'
})
export class FormAsignacionesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormAsignacionesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { }
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;


  ngOnInit(): void {
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
      this.form.controls.usuario.disable();
      this.form.controls.fecha_inicio.disable();
      this.form.controls.fecha_fin.disable();
      this.form.controls.id_pedido_cabecera.disable();   
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
      this.form.controls.usuario.enable();
      this.form.controls.fecha_fin.disable();
      this.form.controls.descripcion_estado.setValue('Recibido');
      this.form.controls.fecha_fin.setValue(null);
      this.form.controls.descripcion_estado.disable();

    }
  }

  form = new FormGroup({
    id_asignaciones: new FormControl('', []),
    usuario: new FormControl('', [
      Validators.required,
    ]),
    fecha_inicio: new FormControl('', [
      Validators.required,
    ]),
    fecha_fin: new FormControl('', [
    ]),
    id_pedido_cabecera: new FormControl('', [
      Validators.required,
    ]),
    descripcion_estado: new FormControl('', [
      Validators.required,
    ])
  })
  

  cerrarModalSinInformacion(cerrar: boolean) {
    if (cerrar) {
      this.dialogRef.close();
    }
  }

  cerrarModalConInformacion() {
    this.dialogRef.close('ok');
  }

  actualizarAsignacion(body: any) {
    console.log('Datos a enviar para editar:', body);
    this.requestservice.put(body, Constantes.apiUpdateAsignaciones)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.usermessage.getToastMessage('success', Constantes.updateResponseMsg).fire();
          this.cerrarModalConInformacion();
        },
        error: (error) => {
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      })
  }

  registrarAsignacion(body: any) {
    this.requestservice.post(body, Constantes.apiCreateAsignaciones)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.usermessage.getToastMessage('success', Constantes.createResponseMsg).fire();
          this.cerrarModalConInformacion();
        },
        error: (error) => {
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
    console.log('Datos a enviar:', body); 


    this.usermessage.questionMessage(Constantes.formQuestion).then((r) => {
      if (r.isConfirmed) {
        if (this.data.tipo == 'editar') {
          
          this.actualizarAsignacion(body);
        }
        else {
          this.registrarAsignacion(body);
        }
      }
    })
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
