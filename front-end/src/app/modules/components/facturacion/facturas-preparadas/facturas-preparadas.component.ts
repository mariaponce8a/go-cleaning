import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { MaterialModule } from '../../../desginModules/material.module';
import { Router } from '@angular/router';
import { FormArray, FormControl, FormGroup } from '@angular/forms';
import { RequestService } from '../../../shared/services/request.service';
import { Constantes } from '../../../config/constantes';
import { map, startWith, Subject, takeUntil } from 'rxjs';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { IusuariosPlataforma, IclientesPlataforma, IdescuentosPlataforma, IserviciosPlataforma } from '../../../shared/interface/datamodels.interface';

@Component({
  selector: 'app-facturas-preparadas',
  standalone: true,
  imports: [
    ColoredBodyHeaderComponent,
    ModalHeaderComponent,
    GlobalButtonsComponent,
    MaterialModule
  ],
  templateUrl: './facturas-preparadas.component.html',
  styleUrl: './facturas-preparadas.component.css'
})
export class FacturasPreparadasComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  dataDelPedido: any;
  dataDelPedidoDetalle: any;

  generalLoading: boolean = false;

  public comboUsuarios: IusuariosPlataforma[] = [];
  public comboClientes: IclientesPlataforma[] = [];
  public combosDescuentos: IdescuentosPlataforma[] = [];
  public ComboClientesCedulas: string[] = [];
  public comboServicios: IserviciosPlataforma[] = [];

  constructor(
    private router: Router,
    private requestserv: RequestService,
    private usermessage: UserMessageService,
    private datatransfer: DataService,
  ) {
  }

  form = new FormGroup({
    id_pedido_cabecera: new FormControl(),
    fecha_pedido: new FormControl(),
    fk_id_usuario: new FormControl(),
    cantidad_articulos: new FormControl(),
    fk_id_cliente: new FormControl(),
    identificacion_cliente: new FormControl(),
    correo_cliente: new FormControl(),
    nombre_cliente: new FormControl(),
    apellido_cliente: new FormControl(),
    infoClienteCompleto: new FormControl(),
    fk_id_descuentos: new FormControl(),
    pedido_subtotal: new FormControl(),
    total: new FormControl(),
    estado_pago: new FormControl(),
    valor_pago: new FormControl(),
    fecha_recoleccion_estimada: new FormControl(),
    hora_recoleccion_estimada: new FormControl(),
    fecha_entrega_estimada: new FormControl(),
    direccion_recoleccion: new FormControl(),
    hora_entrega_estimada: new FormControl(),
    direccion_entrega: new FormControl(),
    tipo_entrega: new FormControl(),
    descEntrega: new FormControl(),
    estado_facturacion: new FormControl(),
    diferenciaPagar: new FormControl(),
  })


  ngOnInit(): void {
    this.getPedido();

    console.log('DETALLE DEL PEDIDO --> ', this.datatransfer.getDatos().fila.id_pedido_cabecera);
  }

  formItemList = new FormGroup({
    itemList: new FormArray<FormGroup>([])
  });

  get itemList() {
    return this.formItemList.controls['itemList'] as FormArray<FormGroup>;
  }

  setearDetalles(detalles: any[]) {

    for (let i of detalles) {
      // Crear una nueva instancia de FormGroup en cada iteración
      let formItem = new FormGroup({
        id_pedido_detalle: new FormControl(),
        fk_id_servicio: new FormControl(),
        libras: new FormControl(),
        precio_servicio: new FormControl(),
        fk_id_pedido: new FormControl(),
        descServicio: new FormControl(),
        descripcion_articulo: new FormControl(),
        cantidad: new FormControl()
      });

      formItem.patchValue(i);

      let itemEncontrado = this.comboServicios.find(s => s.id_servicio == formItem.controls.fk_id_servicio.value);
      formItem.controls.descServicio.setValue(itemEncontrado?.descripcion_servicio);
      this.itemList.push(formItem);
    }

    console.log(this.itemList);
  }

  getPedido() {
    if (this.datatransfer.getDatos() && this.datatransfer.getDatos().fila.id_pedido_cabecera) {
      let url = Constantes.apiGetPedidosXId
      let queryparmas = new Map<string, any>();
      queryparmas.set('id', this.datatransfer.getDatos().fila.id_pedido_cabecera)
      this.requestserv.get(url, queryparmas)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: (value) => {
            this.getAllClientes();
            if (value.respuesta == 0) {
              this.usermessage.getToastMessage('error', 'Problemas al cargar los detalles').fire();
              this.router.navigateByUrl('bds/facturas');
            } else {
              this.dataDelPedido = value.data.pedido;
              this.dataDelPedidoDetalle = value.data.detalle;
              console.log(this.dataDelPedido);
              this.form.patchValue(this.dataDelPedido);
              this.form.controls.infoClienteCompleto.setValue(
                this.form.controls.nombre_cliente.value + " " +
                this.form.controls.apellido_cliente.value + " / " +
                this.form.controls.correo_cliente.value
              );

              this.form.controls.diferenciaPagar.setValue(
                this.form.controls.valor_pago.value && this.form.controls.total.value ?
                  (this.form.controls.total.value - this.form.controls.valor_pago.value) :
                  0);

              this.form.controls.descEntrega.setValue(
                this.form.controls.tipo_entrega.value == "L" ? 'LOCAL' : 'DOMICILIO'
              );
            }
          },
          error: () => {
            this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
          }
        })
    } else {
      this.generalLoading = false;
      this.usermessage.getToastMessage('error', 'Por favor seleccione un pedido').fire();
      this.router.navigateByUrl('bds/facturas');
    }
  }

  getAllClientes() {
    this.requestserv
      .get(Constantes.apiGetAllClientes)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.comboClientes = value.data;
          this.ComboClientesCedulas = this.comboClientes.map(c => c.identificacion_cliente);
          console.log(this.ComboClientesCedulas);
          this.getAllDiscounts();
        },
        error: () => {
          this.generalLoading = false;
          this.router.navigateByUrl('bds/facturas');
          this.usermessage.getToastMessage('error', 'Error al cargar los clientes').fire()
        },
      });
  }


  getAllDiscounts() {
    this.requestserv.get(Constantes.apiGetAllDescuentos)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.combosDescuentos = value.data;
          this.getAllServices();
        },
        error: () => {
          this.generalLoading = false;
          this.router.navigateByUrl('bds/facturas');
          this.usermessage.getToastMessage('error', 'Error al cargar los descuentos').fire()
        }
      });
  }

  getAllServices() {
    this.requestserv.get(Constantes.apiGetAllServices)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.comboServicios = value.data;
          this.setearDetalles(this.dataDelPedidoDetalle);
          this.generalLoading = true;
        },
        error: () => {
          this.generalLoading = false;
          this.router.navigateByUrl('bds/facturas');
          this.usermessage.getToastMessage('error', 'Error al cargar los servicios').fire()
        },
      });
  }

  goBack(evento: string) {
    if (evento) {
      this.router.navigateByUrl('bds/facturas');
    }
  }

  facturar(accion?: string) {

    if (this.form.controls.estado_facturacion.value == 1) {
      this.usermessage.getToastMessage('info', 'El pedido ya se encuentra facturado').fire();
      return;
    }

    let body = {
      "id_pedido_cabecera": this.form.controls.id_pedido_cabecera.value,
      "estado_facturacion": 1
    }

    let numerosFacturas: any[] = this.dataDelPedidoDetalle.map((p: any) =>
      p.id_pedido_detalle
    )
    console.log(body)

    let facturasAejecutar: any[] = []; //= listadoFacturas.join(" - ");

    numerosFacturas.forEach(element => {
      element = "#000" + String(element)
      facturasAejecutar.push(element);
    });

    let stringFacturas = facturasAejecutar.join(" - ");

    this.usermessage.questionMessage(stringFacturas, '¿Está seguro de generar las siguientes facturas?').then((r) => {
      if (r.isConfirmed) {
        console.log('confirmado')
        this.requestserv.put(body, Constantes.ejecutarFacturacion)
          .subscribe({
            next: (value) => {
              console.log(value);
              this.usermessage.getToastMessage('success', 'Factura generada con éxito').fire();
              this.router.navigateByUrl('bds/facturas');
            },
            error: (error) => {
              console.log(error)
              this.usermessage.getToastMessage('error', 'Error al generar la facturación').fire();

            }
          })
      }
    })


  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
