import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IserviciosPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';



@Component({
  selector: 'app-form-servicios',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-servicios.component.html',
  styleUrls: ['./form-servicios.component.css']
})
export class FormServiciosComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  
  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormServiciosComponent>,
    @Inject(MAT_DIALOG_DATA) public data:  IaccionBotones,
  ) { }
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;



  ngOnInit(): void {
    console.log(this.data.fila);  // Verifica si validar_pesaje tiene el valor correcto
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
      const filaConValorTransformado = {
        ...this.data.fila,
        validar_pesaje: this.data.fila.validar_pesaje === 'No validar' ? 'No' : 'Si',
        maximo_articulos: this.data.fila.maximos_articulos ?? undefined
      };
  
      this.form.patchValue(filaConValorTransformado);    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
    }
  } 
  form = new FormGroup({
    id_servicio: new FormControl(''),
    descripcion_servicio: new FormControl('', [Validators.required]),
    costo_unitario: new FormControl('', [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]{1,2})?$')]),
    validar_pesaje: new FormControl('', [Validators.required]),
    maximo_articulos: new FormControl('', [
      Validators.pattern('^[0-9]*$'), // Solo números
      Validators.min(1), // Valor mínimo 1
      Validators.max(150) // Valor máximo 150 
    ])
  });

  

 
  cerrarModalSinInformacion(cerrar: boolean) {
    if (cerrar) {
      this.dialogRef.close();
    }
  }

  cerrarModalConInformacion() {
    this.dialogRef.close('ok');
  }

  editarServicios(body: any) {
    this.requestservice.put(body, Constantes.apiUpdateServices)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.usermessage.getToastMessage('success', Constantes.updateResponseMsg).fire();
          this.cerrarModalConInformacion();
        },
        error: (error) => {
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      });
  }

  registrarServicios(body: any) {
    this.requestservice.post(body, Constantes.apiCreateServices)
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

    this.usermessage.questionMessage(Constantes.formQuestion).then((r) => {
      if (r.isConfirmed) {
        if (this.data.tipo === 'editar') {
          this.editarServicios(body);
        } else {
          this.registrarServicios(body);
        }
      }
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
