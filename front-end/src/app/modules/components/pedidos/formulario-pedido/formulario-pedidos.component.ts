import { Component, KeyValueChanges, OnDestroy, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { Router } from '@angular/router';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { IServicioPedido, IaccionBotones, IclientesPlataforma, IdescuentosPlataforma, IserviciosPlataforma, IusuariosPlataforma } from '../../../shared/interface/datamodels.interface';
import { LocalStorageEncryptationService } from '../../../shared/services/local-storage-encryptation.service';
import { Constantes } from '../../../config/constantes';
import { RequestService } from '../../../shared/services/request.service';
import { FormGroup, FormArray, FormControl, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Observable, Subject } from 'rxjs';
import { count, map, startWith, takeUntil } from 'rxjs/operators';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { HttpClient } from '@angular/common/http';
import { AsyncPipe } from '@angular/common';
import { FormClientesComponent } from '../../clientes/form-clientes/form-clientes.component';
import { MatDialog } from '@angular/material/dialog';
import { MatOptionSelectionChange } from '@angular/material/core';
import { MatSelectChange } from '@angular/material/select';
import { SelectionChange } from '@angular/cdk/collections';
import { validarFechas } from '../../../shared/validators/custom-val';


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
  public comboServicios: IserviciosPlataforma[] = [];
  public clientesExistentesLong: number = 0;

  public itemsDelPedidoArr: IServicioPedido[] = [];
  public itemIndividualPedido: IServicioPedido = {
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
    fk_id_cliente: new FormControl(0, [Validators.required]),
    identificacionCliente: new FormControl(),
    nombreCompletoCliente: new FormControl(),
    fk_id_descuentos: new FormControl(),
    pedido_subtotal: new FormControl(0),
    total: new FormControl(0),
    estado_pago: new FormControl(),
    valor_pago: new FormControl(0),
    fecha_recoleccion_estimada: new FormControl(),
    hora_recoleccion_estimada: new FormControl(''),
    direccion_recoleccion: new FormControl(),
    fecha_entrega_estimada: new FormControl('', [Validators.required]),
    hora_entrega_estimada: new FormControl('', [Validators.required]),
    direccion_entrega: new FormControl(),
    tipo_entrega: new FormControl(),
  },
    // {
    //   validators: validarFechas() //validador personalizado
    // }
  );

  get fechaRecoleccionInvalida() {
    return this.form.errors?.['fechaRecoleccionInvalida'];
  }

  get fechaEntregaInvalida() {
    return this.form.errors?.['fechaEntregaInvalida'];
  }

  formItemList = new FormGroup({
    itemList: new FormArray<FormGroup>([])
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
    this.calcularTotalArticulos();
    this.form.controls.usuario.disable();
    let fakeDiscount = {
      value: 0
    }
    this.descuentoSeleccionado(fakeDiscount)
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
          this.getAllServices();
        },
        error: () => {
          this.usermessage.getToastMessage('error', 'Error al cargar los descuentos').fire()
        }
      });
  }

  getAllServices() {
    this.requestServ.get(Constantes.apiGetAllServices)
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
    this.formItemList.reset();
  }

  executeActionItems(event: any) {
    console.log(event);
    if (event == 'add') {
      let formItem = new FormGroup({
        fk_id_servicio: new FormControl(0, [Validators.required]),
        libras: new FormControl(0),
        precio_servicio: new FormControl(0),
        fk_id_pedido: new FormControl(),
        descripcion_articulo: new FormControl('', [Validators.required]),
        cantidad: new FormControl(0, [Validators.required, Validators.pattern(/^\d+$/)]),
        validarPesaje: new FormControl(0),
        costoUnitario: new FormControl(0),
        maximo: new FormControl(0)
      });
      this.itemList.push(formItem);

      this.form.get('pedido_subtotal')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });
      this.form.get('total')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      this.form.get('valor_pago')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      this.form.get('fk_id_descuentos')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });
      this.form.get('cantidad_articulos')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      // Suscribirse a los cambios de los controles
      formItem.get('libras')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      formItem.get('costoUnitario')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      formItem.get('validarPesaje')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

      formItem.get('maximo')?.valueChanges.subscribe(() => {
        this.calcularSubtotal(formItem);
      });

    } else {
      console.log(this.itemList.value);
    }
  }

  calcularArticuloYsubtotal(items?: FormGroup) {
    if (items) {
      this.calcularSubtotal(items);
    }
    this.calcularTotalArticulos();
  }

  redondearValores(valor: number, decimales: number = 2): number {
    return Math.round(valor * Math.pow(10, decimales)) / Math.pow(10, decimales);
  }

  calcularSubtotal(item: FormGroup) {
    const validarPesaje = item.get('validarPesaje')?.value;
    const libras = item.get('libras')?.value;
    const costoUnitario = item.get('costoUnitario')?.value;
    const cantidad = item.get('cantidad')?.value;

    // Realiza el cálculo del subtotal
    const subtotal = validarPesaje == 1
      ? Number(costoUnitario) * libras
      : Number(costoUnitario) * cantidad;

    // Actualiza el valor de precio_servicio en el FormControl
    item.get('precio_servicio')?.setValue(subtotal, { emitEvent: false });

  }

  get itemList() {
    return this.formItemList.controls['itemList'] as FormArray<FormGroup>;
  }


  removeItem(indice: number) {
    this.itemList.removeAt(indice);
    this.calcularTotalArticulos();
  }

  cambiarAdelantoPago(event: any) {
    console.log(event);
    switch (event.value) {
      case 'P': //PARCIAL
        this.form.get('valor_pago')?.setValue(this.redondearValores(Number(this.form.get('total')?.value) / 2), { emitEvent: false });
        break;
      case 'C': //COMPLETO
        this.form.get('valor_pago')?.setValue(this.redondearValores(Number(this.form.get('total')?.value)), { emitEvent: false });
        break;
      case 'F': //AL FINALIZAR
        this.form.get('valor_pago')?.setValue(0, { emitEvent: false });
        break;
      default:
        this.form.get('valor_pago')?.setValue(0, { emitEvent: false });
        break;
    }
    console.log(this.form.getRawValue());
  }


  public totalArticulos: number = 0;
  public subtotal: number = 0;
  calcularTotalArticulos() {
    this.totalArticulos = 0;
    this.subtotal = 0;

    let formularios = this.itemList.value;
    //  calcular cantidades y subtotal
    formularios.map(item => {
      this.totalArticulos += item.cantidad
      this.subtotal += item.precio_servicio
    });

    console.log(this.form.getRawValue())

    this.form.get('pedido_subtotal')?.setValue(this.redondearValores(this.subtotal), { emitEvent: false });
    this.form.get('cantidad_articulos')?.setValue(this.totalArticulos, { emitEvent: false });
    // calcular total
    this.form.get('total')?.setValue(
      !this.form.get('fk_id_descuentos')?.value
        ? this.redondearValores(this.subtotal)
        : this.redondearValores(this.subtotal - (this.subtotal * this.descuentoObtenido))
      , { emitEvent: false });



    this.form.get('cantidad_articulos')?.setValue(this.totalArticulos, { emitEvent: false });


  }



  getSelectedClient(event: MatOptionSelectionChange) {
    console.log(event.source.value);
    let datosCompletosCliente = this.comboClientes.find(c => c.identificacion_cliente == event.source.value);
    this.form.controls.fk_id_cliente.setValue(Number(datosCompletosCliente?.id_cliente));
    this.form.controls.nombreCompletoCliente.setValue(datosCompletosCliente?.nombre_cliente + ' ' + datosCompletosCliente?.apellido_cliente + ' / ' + datosCompletosCliente?.correo_cliente);
    this.habilitarFormularioRestante = true;
  }

  validacionMaxArticulos: number = 0;
  validarMaximoArticulosPorServicios() {

  }


  servicioSeleccionado(event: any, items: FormGroup) {
    console.log(event.value, items);
    let itemEncontrado = this.comboServicios.find(s => s.id_servicio == event.value);
    if (itemEncontrado) {
      items.controls['validarPesaje'].setValue(itemEncontrado.validar_pesaje);
      items.controls['costoUnitario'].setValue(itemEncontrado.costo_unitario);
      items.controls['maximo'].setValue(itemEncontrado.maximo_articulos);
    }
    this.calcularTotalArticulos();
  }

  public descuentoObtenido = 0
  descuentoSeleccionado(event: any) {
    console.log(this.combosDescuentos, event);
    let itemEncontrado = this.combosDescuentos.find(d => d.id_tipo_descuento == event);
    if (itemEncontrado) {
      this.descuentoObtenido = Number(itemEncontrado.cantidad_descuento);
    }
    this.form.get('fk_id_descuentos')?.setValue(event, { emitEvent: false });
    this.calcularTotalArticulos();
    this.cambiarAdelantoPago({ value: this.form.controls.estado_pago.value });
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

    if (this.form.invalid) {
      this.usermessage.getToastMessage('info', Constantes.formInvalidMessage).fire();
      this.form.markAllAsTouched();
      return;
    }

    if (this.itemList.length == 0) {
      this.usermessage.getToastMessage('info', 'Debe agregar servicios al pedido').fire();
      this.form.markAllAsTouched();
      return;
    }

    if (this.formItemList.invalid) {
      this.usermessage.getToastMessage('info', 'Servicios inválidos').fire();
      this.form.markAllAsTouched();
      return;
    }

    let pedido = {
      "fecha_pedido": new Date(),
      "fk_id_usuario": this.form.controls.fk_id_usuario.value ?? null,
      "cantidad_articulos": this.form.controls.cantidad_articulos.value ?? null,
      "fk_id_cliente": this.form.controls.fk_id_cliente.value ?? null,
      "fk_id_descuentos": Number(this.form.controls.fk_id_descuentos.value) ?? null,
      "pedido_subtotal": this.form.controls.pedido_subtotal.value ?? 0,
      "estado_pago": this.form.controls.estado_pago.value ?? null,
      "valor_pago": this.form.controls.valor_pago.value ?? 0,
      "total": this.form.controls.total.value ?? 0,
      "fecha_recoleccion_estimada": this.form.controls.fecha_recoleccion_estimada.value ?? null,
      "hora_recoleccion_estimada": this.form.controls.hora_recoleccion_estimada.value ?? null,
      "direccion_recoleccion": this.form.controls.direccion_recoleccion.value ?? null,
      "fecha_entrega_estimada": this.form.controls.fecha_entrega_estimada.value ?? null,
      "hora_entrega_estimada": this.form.controls.fecha_entrega_estimada.value ?? null,
      "direccion_entrega": this.form.controls.direccion_entrega.value ?? null,
      "tipo_entrega": this.form.controls.tipo_entrega.value ?? null,
      "detallePedido": JSON.stringify(this.formItemList.getRawValue().itemList)
    }

    console.log(pedido)

    this.usermessage.questionMessage('¿Está seguro que desea guardar el pedido? Una vez aceptado no podrá modificarlo').then(r => {
      if (r.isConfirmed) {
        //TODO : LOGICA DE INSERCION

      }
    })

  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
