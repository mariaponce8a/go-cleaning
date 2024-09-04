import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IrecomendacionesPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';

@Component({
  selector: 'app-form-estados',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-recomendaciones.component.html',
  styleUrl: './form-recomendaciones.component.css'
})
export class FormRecomendacionesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormRecomendacionesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { }
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;


  ngOnInit(): void {
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
      this.form.controls.descripcion_material.enable();
    }
  }

  form = new FormGroup({
    id_recomendacion_lavado: new FormControl('', []),
    descripcion_material: new FormControl('', [
      Validators.required,
      Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]*$')
    ]),
    descripcion_servicio: new FormControl('', [
      Validators.required,
      Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]*$')
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

  actualizarRecomendacion(body: any) {
    console.log('Datos a enviar para editar:', body);
    this.requestservice.put(body, Constantes.apiUpdateRecomendaciones)
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

  registrarRecomendacion(body: any) {
    this.requestservice.post(body, Constantes.apiCreateRecomendaciones)
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
          this.actualizarRecomendacion(body);
        }
        else {
          this.registrarRecomendacion(body);
        }
      }
    })
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
