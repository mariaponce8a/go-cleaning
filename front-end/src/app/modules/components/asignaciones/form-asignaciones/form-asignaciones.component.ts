import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IAsignacionEmpleadosPlataforma, IestadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { Interface } from 'readline';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-form-asignaciones',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent, CommonModule,
        IonicModule
  ],
  templateUrl: './form-asignaciones.component.html',
  styleUrl: './form-asignaciones.component.css'
})
export class FormAsignacionesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  public comboEstados: IestadosPlataforma[] = [];

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
    this.getAllEstados();
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
      this.form.controls.fecha_fin.setValue(null);

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
  
  
  getAllEstados() {
    this.requestservice
      .get(Constantes.apiGetAllEstados)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => { 
          this.comboEstados = value.data.filter((estado: any) => 
            estado.descripcion_estado !== 'Recibido' && 
            estado.descripcion_estado !== 'Por recoger'
          );
          console.log(this.comboEstados); 
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los servicios').fire()
        },
      });
  }

  getSelectedEstado(event: any, items: FormGroup) {
    console.log(event.value, items);
    let itemEncontrado = this.comboEstados.find(s => s.descripcion_estado == event.value);
    if (itemEncontrado) {
      items.controls['descripcion_estado'].setValue(itemEncontrado.descripcion_estado);
    }
  }

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
          if (body.descripcion_estado === 'Finalizado') {
            console.log('Llamando a enviarMensajeWhatsApp con ID:', body.id_pedido_cabecera);
            this.enviarMensajeWhatsApp(body.id_pedido_cabecera);
          }
          this.cerrarModalConInformacion();
        },
        error: (error) => {
          console.error('Error en la actualización o envío:', error);
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      });
}


  enviarMensajeWhatsApp(id_pedido_cabecera: number) {
    this.requestservice.post({ "id_pedido_cabecera": id_pedido_cabecera }, Constantes.apiSendMessage)
    
      .subscribe({
        next: (response) => {
          this.usermessage.getToastMessage('success', 'Mensaje de WhatsApp enviado correctamente').fire();
        },
        error: (error) => {
          this.usermessage.getToastMessage('error', 'Error al enviar el mensaje de WhatsApp').fire();
        }
      });
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
