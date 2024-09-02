import { Component, OnDestroy, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { MatDialog } from '@angular/material/dialog';

import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { ITitulosTabla, IaccionBotones, IpedidosJoin } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listar-pedidos',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent
  ],
  templateUrl: './listar-pedidos.component.html',
  styleUrl: './listar-pedidos.component.css'
})
export class ListarPedidosComponent implements OnInit, OnDestroy {

  titulosTabla: ITitulosTabla[] = [
    {
      value: "fecha_pedido",
      viewValue: "Fecha pedido"
    },
    {
      value: "usuario",
      viewValue: "Empleado"
    },
    {
      value: "cantidad_articulos",
      viewValue: "Cantidad de artículos"
    },
    {
      value: "identificacion_cliente",
      viewValue: "ID: Cliente"
    },
    {
      value: "nombre_cliente",
      viewValue: "Nombre cliente"
    },
    {
      value: "apellido_cliente",
      viewValue: "Apellido cliente"
    },
    {
      value: "tipo_descuento_desc",
      viewValue: "Descuento"
    },
    {
      value: "pedido_subtotal",
      viewValue: "Sub total"
    },
    {
      value: "valor_pago",
      viewValue: "Total"
    },
    {
      value: "estado_pago",
      viewValue: "Pago"
    },
    {
      value: "tipo_entrega",
      viewValue: "Entrega"
    }
  ]

  valoresDeTabla: IpedidosJoin[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog
  ) { }

  ngOnInit(): void {
    this.getAllPedidos();
  }

  getAllPedidos() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetAllPedidos)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;

        },
        error: () => {
          this.loadingTable = false;
        }
      })
  }

  manejarEventosBotones(evento: IaccionBotones) {
    console.log(evento);
    let dialogRef;
    switch (evento.tipo) {
      case 'editar':

        break;
      case 'crear':

        break;

      case 'eliminar':
        let body = {
          id: evento.fila.id_usuario
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, 'insertar api eliminar')
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllPedidos();
                },
                error: (error) => {
                  this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
                }
              })
          }
        })
        break;

    }

  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
