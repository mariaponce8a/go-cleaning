import { Component, OnDestroy, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { Router } from '@angular/router';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { IServicioPedido, IaccionBotones, IclientesPlataforma, IdescuentosPlataforma, IusuariosPlataforma } from '../../../shared/interface/datamodels.interface';
import { LocalStorageEncryptationService } from '../../../shared/services/local-storage-encryptation.service';
import { Constantes } from '../../../config/constantes';
import { RequestService } from '../../../shared/services/request.service';
import { FormGroup, FormControl, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Observable, Subject } from 'rxjs';
import { count, map, startWith, takeUntil } from 'rxjs/operators';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { HttpClient } from '@angular/common/http';
import { AsyncPipe } from '@angular/common';
import { FormClientesComponent } from '../../clientes/form-clientes/form-clientes.component';
import { MatDialog } from '@angular/material/dialog';
import { MatOptionSelectionChange } from '@angular/material/core';
import { MatSelectChange } from '@angular/material/select';


@Component({
  selector: 'app-formulario-pedidos',
  standalone: true,
  imports: [
    MaterialModule,
    ColoredBodyHeaderComponent,
    ModalHeaderComponent,
    GlobalButtonsComponent,
    ReactiveFormsModule,
    AsyncPipe,
    FormsModule,
    FormClientesComponent
  ],
  templateUrl: './formulario-pedidos.component.html',
  styleUrl: './formulario-pedidos.component.css'
})
export class FormularioPedidosComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  public propositoPagina: string = 'Formulario';
  public formInfo?: IaccionBotones;

  public comboUsuarios: IusuariosPlataforma[] = [];
  public comboClientes: IclientesPlataforma[] = [];
  public combosDescuentos: IdescuentosPlataforma[] = [];
  public ComboClientesCedulas: string[] = [];
  public clientesExistentesLong: number = 0;

  public itemsDelPedidoArr: IServicioPedido[] = [];
  public itemIndividualPedido?: IServicioPedido = {
    descripcion_articulo: "",
    fk_id_pedido: 0,
    fk_id_servicio: 0,
    libras: 0,
    precio_servicio: 0,
  };

  filteredOptions!: Observable<string[]>;

  public usuarioPlataforma: string | null = '';
  public idUsuarioPlataforma: number = 0;

  public habilitarFormularioRestante: boolean = false;

  constructor(
    private http: HttpClient,
    private router: Router,
    private datatransfer: DataService,
    public localencript: LocalStorageEncryptationService,
    private requestServ: RequestService,
    private usermessage: UserMessageService,
    private dialog: MatDialog
  ) {
    this.usuarioPlataforma = this.localencript.getLocalStorage(Constantes.usuarioKey);
    this.idUsuarioPlataforma = Number(this.localencript.getLocalStorage(Constantes.idusuarioKey));
  }

  form = new FormGroup({
    fecha_pedido: new FormControl(),
    fk_id_usuario: new FormControl(),
    usuario: new FormControl({ value: '', disabled: false }),
    cantidad_articulos: new FormControl(0),
    fk_id_cliente: new FormControl(),
    identificacionCliente: new FormControl(),
    nombreCompletoCliente: new FormControl(),
    fk_id_descuentos: new FormControl(),
    pedido_subtotal: new FormControl(0),
    total: new FormControl(0),
    estado_pago: new FormControl(),
    valor_pago: new FormControl(0),
    fecha_recoleccion_estimada: new FormControl(),
    hora_recoleccion_estimada: new FormControl(),
    direccion_recoleccion: new FormControl(),
    fecha_entrega_estimada: new FormControl(),
    hora_entrega_estimada: new FormControl(),
    direccion_entrega: new FormControl(),
    tipo_entrega: new FormControl()
  });

  public _filter(value: string): string[] {
    const filterValue = value.toLowerCase();
    let valorEncontrado = this.ComboClientesCedulas.filter(option => option.toLowerCase().includes(filterValue));
    this.clientesExistentesLong = valorEncontrado.length;
    console.log(this.clientesExistentesLong)
    if (this.clientesExistentesLong == 0) {
      this.form.controls.identificacionCliente.markAsTouched();
    }
    return valorEncontrado;
  }

  ngOnInit(): void {
    console.log(this.datatransfer.getDatos());
    this.formInfo = this.datatransfer.getDatos();
    this.getAllClientes();

    this.form.controls.usuario.disable();
    if (this.formInfo?.tipo == 'crear') {
      this.form.controls.usuario.setValue(this.usuarioPlataforma);
      this.form.controls.fk_id_usuario.setValue(this.idUsuarioPlataforma);
    } else {
      this.form.controls.usuario.enable();
    }
  }

  getUsuarios() {
    this.requestServ.get(Constantes.apiGetAllUsers)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.comboUsuarios = value.data;
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los usuarios').fire()
        }
      })
  }

  getAllClientes() {
    this.requestServ
      .get(Constantes.apiGetAllClientes)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.comboClientes = value.data;
          this.ComboClientesCedulas = this.comboClientes.map(c => c.identificacion_cliente);
          console.log(this.ComboClientesCedulas);

          this.filteredOptions = this.form.controls.identificacionCliente.valueChanges.pipe(
            startWith(''),
            map(value => this._filter(value || '')),
          )

          this.getAllDiscounts();

        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los clientes').fire()
        },
      });
  }

  getAllDiscounts() {
    this.requestServ.get(Constantes.apiGetAllDescuentos)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.combosDescuentos = value.data;
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los descuentos').fire()
        }
      });
  }

  setearDireccionEntrega(event: MatSelectChange) {
    console.log(event);
    if (event.value == 'L') {
      this.form.controls.direccion_entrega
        .setValue('(local) Leonardo Murialdo N57-199 yMiguel Valdiviezo-Kennedy Quito Pichincha Ecuador');
    } else {
      this.form.controls.direccion_entrega.setValue('');
    }
  }

  cambiandoCliente() {
    this.form.controls.fk_id_cliente.setValue(0);
    this.form.controls.nombreCompletoCliente.setValue('');
    this.habilitarFormularioRestante = false;
  }

  getSelectedClient(event: MatOptionSelectionChange) {
    console.log(event.source.value);
    let datosCompletosCliente = this.comboClientes.find(c => c.identificacion_cliente == event.source.value);
    console.log(datosCompletosCliente);
    this.form.controls.fk_id_cliente.setValue(datosCompletosCliente?.id_cliente);
    this.form.controls.nombreCompletoCliente.setValue(datosCompletosCliente?.nombre_cliente + ' ' + datosCompletosCliente?.apellido_cliente + ' / ' + datosCompletosCliente?.correo_cliente);
    this.habilitarFormularioRestante = true;
  }

  openAgregarCliente() {
    let dialog = this.dialog.open(FormClientesComponent, {
      data: { tipo: 'crear', fila: {} },
      width: '600px',
      disableClose: true,
    })

    dialog.afterClosed().subscribe((r) => {
      if (r == 'ok') {
        this.getAllClientes();
      }
    })
  }

  goBack(evento: string) {
    if (evento) {
      this.router.navigateByUrl('bds/pedidos');
    }
  }


  guardar() {

  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
