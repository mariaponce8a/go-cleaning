import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { pipe, Subject, takeUntil } from 'rxjs';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, ImaterialesPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';

@Component({
  selector: 'app-form-materiales',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent
  ],
  templateUrl: './form-materiales.component.html',
  styleUrl: './form-materiales.component.css'
})
export class FormMaterialesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  imageBase64: string | null = null;
  selectedFile: File | null = null;


  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormMaterialesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { }
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;


  ngOnInit(): void {
    console.log(this.data);
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
      console.log('Imagen recibida:', this.data.fila.imagen);

      if (this.data.fila.imagen) {
        this.imageBase64 = this.data.fila.imagen; 
      }
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
      this.form.controls.descripcion_material.enable();
    }
  }

  form = new FormGroup({
    id_material: new FormControl('', []),
    descripcion_material: new FormControl('', [
      Validators.required,
      Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]*$')
    ]),
    imagen: new FormControl('', [Validators.required])
  });


  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      // Tipos MIME permitidos
      const validImageTypes = ['image/jpeg', 'image/png'];
      const pdfType = 'image/pdf';
      const heicType = 'image/heic'; 
  
      if (file.type === pdfType || file.type === heicType) {
        this.form.controls.imagen.setErrors({ invalidFileType: true });
        this.usermessage.getToastMessage('error', 'El archivo PDF o HEIC no es permitido. Solo se permiten imágenes JPEG, JPG y PNG.').fire();
        this.form.controls.imagen.setValue(null);
        return; 
      }
  
      if (!validImageTypes.includes(file.type)) {
        this.form.controls.imagen.setErrors({ invalidFileType: true });
        this.usermessage.getToastMessage('error', 'Tipo de archivo no permitido. Solo se permiten imágenes JPEG, JPG y PNG.').fire();
        this.form.controls.imagen.setValue(null);
        return;
      }

      if (file.size > 1024 * 1024) {
        this.form.controls['imagen'].setErrors({ fileTooLarge: true });
        this.usermessage.getToastMessage('error','La imagen es muy grande').fire();

        return;
    }
  
      const reader = new FileReader();
      reader.onloadend = () => {
        this.imageBase64 = (reader.result as string).split(',')[1]; 
        this.form.controls.imagen.setValue(this.imageBase64);
      };
      reader.readAsDataURL(file); 
    } else {
      this.form.controls.imagen.setValue(null);
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

  editarMaterial(body: any) {
    console.log('Datos a enviar para editar:', body);
    this.requestservice.put(body, Constantes.apiUpdateMaterial)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
           console.log('Respuesta del servidor:', value);
           console.log('Material actualizado:', value.respuesta);
          this.usermessage.getToastMessage('success', Constantes.updateResponseMsg).fire();
          this.cerrarModalConInformacion();
        },
        error: (error) => {
          console.error('Error al editar material:', error);
          this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
        }
      })
  }

  registrarMaterial(body: any) {
    this.requestservice.post(body, Constantes.apiCreateMaterial)
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
  
    const requestBody = {
      id_material: this.form.controls.id_material.value || '',
      descripcion_material: this.form.controls.descripcion_material.value || '',
      imagen: this.imageBase64
    };
  
    this.usermessage.questionMessage(Constantes.formQuestion).then((r) => {
      if (r.isConfirmed) {
        if (this.data.tipo === 'editar') {
          this.editarMaterial(requestBody); // Pasar el objeto en lugar del FormData
        } else {
          this.registrarMaterial(requestBody); // Pasar el objeto en lugar del FormData
        }
      }
    });
  }
  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
