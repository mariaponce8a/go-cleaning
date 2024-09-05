import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IRecomendacionesPlataforma} from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';

@Component({
  selector: 'app-form-descuentos',
  standalone: true,  
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-descuentos.component.html',
  styleUrl: './form-descuentos.component.css'   // Cambiado a `styleUrls`
})
export class FormDescuentosComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();

  constructor(
    private userMessage: UserMessageService,
    private requestService: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormDescuentosComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) {}

  tituloPorAccion: string = 'Formulario de Descuentos';
  hide: boolean = false;



  ngOnInit(): void {
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);  // Cargar datos existentes si se está editando
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
    }
  }

  form = new FormGroup({
    id_tipo_descuento: new FormControl(''),
    tipo_descuento_desc: new FormControl('', [Validators.required, ]),
    cantidad_descuento: new FormControl('', [Validators.required, Validators.min(0), Validators.max(99.99)])
  })

  cerrarModalSinInformacion(cerrar: boolean) {
    if (cerrar) {
      this.dialogRef.close();
    }
  }

  cerrarModalConInformacion() {
    this.dialogRef.close('ok');
  }

  editarDescuento(body: any) {
    this.requestService.put(body, Constantes.apiUpdateDescuentos) 
    .pipe(takeUntil(this.destroy$))
    .subscribe({
      next: (value) => {
        this.userMessage.getToastMessage('success', Constantes.updateResponseMsg).fire();
        this.cerrarModalConInformacion();
      },
      error: (error) => {
        this.userMessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
      }
    })
}
  crearDescuento(body: any) {
    this.requestService.post(body, Constantes.apiCreateDescuentos) // Reemplazar con la URL de tu API
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.userMessage.getToastMessage('success', 'Descuento creado con éxito').fire();
          this.cerrarModalConInformacion();
        },
        error: () => {
          this.userMessage.getToastMessage('error', 'Error al crear el descuento').fire();
        }
      });
  }

  guardar() {
    if (this.form.invalid) {
      this.userMessage.getToastMessage('info', Constantes.formInvalidMessage).fire();
      this.form.markAllAsTouched();
      return;
    }

    let body = this.form.getRawValue();
    
    this.userMessage.questionMessage('¿Está seguro de que desea guardar el descuento?').then((r) => {
      if (r.isConfirmed) {
        if (this.data.tipo === 'editar') {
          this.editarDescuento(body);
        } else {
          this.crearDescuento(body);
        }
      }
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}