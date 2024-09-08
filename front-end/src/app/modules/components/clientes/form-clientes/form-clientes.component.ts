import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import {pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IclientesPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';

@Component({
  selector: 'app-form-clientes',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-clientes.component.html',
  styleUrl: './form-clientes.component.css'
})


export class FormClientesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormClientesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { }

  tituloPorAccion: string = 'Formulario Cliente';
  hide: boolean = false;

  ngOnInit(): void {
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
      this.form.controls.identificacion_cliente.valueChanges.subscribe(value => {
        this.validarIdentificacion();
      });
    } else {
      this.form.controls.identificacion_cliente.valueChanges.subscribe(value => {
            this.validarIdentificacion();
          });
    }
  }
  
  validarIdentificacion = () => {
    const tipoIdentificacion = this.form.controls.tipo_identificacion_cliente.value;
    const identificacion = this.form.controls.identificacion_cliente.value;
  
    this.form.controls.identificacion_cliente.setErrors(null);
  
    if (tipoIdentificacion === 'CI' && identificacion !== null) {
      if (identificacion.length !== 10 || isNaN(Number(identificacion))) {
        this.form.controls.identificacion_cliente.setErrors({ ciInvalido: true });
      }
    } else if (tipoIdentificacion === 'RUC' && identificacion !== null) {
      if (identificacion.length !== 13 || !identificacion.endsWith('001')) {
        this.form.controls.identificacion_cliente.setErrors({ rucIncorrecto: true });
      }
    } else if (tipoIdentificacion === 'PASAPORTE' && identificacion !== null) {
      const pasaporteRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/;
      if (!pasaporteRegex.test(identificacion)) {
        this.form.controls.identificacion_cliente.setErrors({ pasaporteInvalido: true });
      }
    }
  }
  form = new FormGroup({
    id_cliente: new FormControl(''),
    identificacion_cliente: new FormControl('', [Validators.required]),
    tipo_identificacion_cliente: new FormControl('', [Validators.required]),
    nombre_cliente: new FormControl('', [Validators.required]),
    apellido_cliente: new FormControl('', [Validators.required]),
    telefono_cliente: new FormControl('', [Validators.required, Validators.pattern('^[0-9]{10}$')]),
    correo_cliente: new FormControl('', [Validators.required, Validators.email]),
    });

  

  cerrarModalSinInformacion(cerrar: boolean) {
    if (cerrar) {
      this.dialogRef.close();
    } 
  }

  cerrarModalConInformacion() {
    this.dialogRef.close('ok');
  }

  actualizarCliente(body: any) {
    this.requestservice.put(body, Constantes.apiUpdateCliente)
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

  registrarCliente(body: any) {
    this.requestservice.post(body, Constantes.apiCreateCliente)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.usermessage.getToastMessage('success', Constantes.createResponseMsg).fire();
          this.cerrarModalConInformacion();
        },
        error: (error) => {
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      });
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
          this.actualizarCliente(body);
        } else {
          this.registrarCliente(body);
        }
      }
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
