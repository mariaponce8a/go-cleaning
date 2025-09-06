import { Component, Inject, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RequestService } from '../../../shared/services/request.service';
import { MaterialModule } from '../../../desginModules/material.module';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { IaccionBotones, IrecomendacionesPlataforma, ImaterialesPlataforma, IserviciosPlataforma } from '../../../shared/interface/datamodels.interface';
import { Constantes } from '../../../config/constantes';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { AsyncPipe, CommonModule } from '@angular/common';
import { FormGroup, FormArray, FormControl, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Observable, Subject } from 'rxjs';
import { MatOptionSelectionChange } from '@angular/material/core';
import {MatInputModule} from '@angular/material/input';
import {MatSelectModule} from '@angular/material/select';
import {MatFormFieldModule} from '@angular/material/form-field';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { count, map, startWith, takeUntil } from 'rxjs/operators';
import { IonicModule } from '@ionic/angular';


@Component({
  selector: 'app-form-estados',
  standalone: true,
  imports: [
    ModalHeaderComponent,
    MaterialModule,
    ColoredBodyHeaderComponent,
    GlobalButtonsComponent,
    ReactiveFormsModule,
    AsyncPipe,
    MatFormFieldModule, 
    MatSelectModule, 
    MatInputModule,
    FormsModule,
    MatAutocompleteModule,
     CommonModule,
        IonicModule
  ],
  
  templateUrl: './form-recomendaciones.component.html',
  styleUrl: './form-recomendaciones.component.css'
})
export class FormRecomendacionesComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  public comboMaterial: ImaterialesPlataforma[] = [];
  public comboServicios: IserviciosPlataforma[] = [];
  public comboDescripcionServicios:string[] = [];
  public serviciosExistentesLong: number = 0;
  filteredOptions!: Observable<string[]>;


  constructor(
    private usermessage: UserMessageService,
    private requestservice: RequestService,
    private router: Router,
    public dialogRef: MatDialogRef<FormRecomendacionesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: IaccionBotones,
  ) { 
  }
  tituloPorAccion: string = 'Formulario';
  hide: boolean = false;

  formItemList = new FormGroup({
    itemList: new FormArray<FormGroup>([])
  });

  get itemList() {
    return this.formItemList.controls['itemList'] as FormArray<FormGroup>;
  }

  ngAfterViewInit(): void {
    this.getAllMaterials();
  }

  ngOnInit(): void {
    console.log(this.data);
    this.getAllMaterials();
    this.getAllServices();
    if (this.data.tipo == 'editar') {
      this.tituloPorAccion = Constantes.modalHeaderMensajeEditar;
      this.form.patchValue(this.data.fila);
    } else {
      this.tituloPorAccion = Constantes.modalHeaderMensajeCrear;
      this.form.controls.descripcion_material.enable();
    }

  }

  public _filter(value: string): string[] {
    const filterValue = value.toLowerCase();
    const valorEncontrado = this.comboDescripcionServicios.filter(option => option.toLowerCase().includes(filterValue));
    this.serviciosExistentesLong = valorEncontrado.length;
    console.log(this.serviciosExistentesLong);
    
    if (this.serviciosExistentesLong === 0) {
      this.form.controls.descripcion_servicio.markAsTouched();
    }
    
    return valorEncontrado;
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
  

  getSelectedService(event: MatOptionSelectionChange) {
    console.log(event.source.value);
    this.form.controls.descripcion_servicio.setValue(event.source.value);
  }

  getSelectedMaterial(event: any, items: FormGroup) {
    console.log(event.value, items);
    let itemEncontrado = this.comboMaterial.find(s => s.descripcion_material == event.value);
    if (itemEncontrado) {
      items.controls['descripcion_material'].setValue(itemEncontrado.descripcion_material);
    }
  }
  
  getAllServices() {
    this.requestservice.get(Constantes.apiGetAllServices)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.comboServicios = value.data;
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los servicios').fire()
        },
      });
  }
    

  getAllMaterials() { 
    this.requestservice
      .get(Constantes.apiGetAllMaterials)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => { 
          this.comboMaterial = value.data;
          console.log(this.comboMaterial); 
          this.comboDescripcionServicios = this.comboServicios.map(c => c.descripcion_servicio);
          this.filteredOptions= this.form.controls.descripcion_servicio.valueChanges.pipe(
            startWith(''),
            map(value => this._filter(value || '')),
          )
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los servicios').fire()
        },
      });
  }

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
