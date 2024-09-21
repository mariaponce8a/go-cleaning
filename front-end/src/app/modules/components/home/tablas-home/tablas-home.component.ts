import { Component, EventEmitter, Output, OnDestroy, Input, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { ITitulosTabla, IpedidosJoin, IestadosPlataforma, IAsignacionEmpleadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { Router } from '@angular/router';
import { DataService } from '../../../shared/services/dataTransfer.service';

@Component({
  selector: 'app-tablas-home',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent,
  ],
  templateUrl: './tablas-home.component.html',
  styleUrl: './tablas-home.component.css'
})
export class TablasHomeComponent implements OnInit, OnDestroy {


  titulosTabla: ITitulosTabla[] = [
    {
      value: "fecha_pedido",
      viewValue: "Fecha pedido"
    }, 
    {
      value: "nombre_usuario_completo",
      viewValue: "Empleado"
    },
    {
      value: "cantidad_articulos",
      viewValue: "# Artículos"
    },
    {
      value: "identificacion_cliente",
      viewValue: "ID Cliente"
    },
    {
      value: "nombre_cliente_completo",
      viewValue: "Nombre cliente"
    },
    {
      value: "descripcion_estado",
      viewValue: "Estado pedido"
    },
    {
      value: "estado_pago",
      viewValue: "Estado pago"
    }, 
    {
      value: "valor_pago",
      viewValue: "Valor pago"
    },
    {
      value: "fecha_recoleccion_estimada",
      viewValue: "Fecha recolección"
    },
    {
      value: "fecha_entrega_estimada",
      viewValue: "Entrega estimada"
    },
    {
      value: "direccion_entrega",
      viewValue: "Dirección entrega"
    },
    {
      value: "tipo_entrega",
      viewValue: "Tipo entrega"
    }
  ]

  valoresDeTabla: IpedidosJoin[] = [];
  rowClasses: { [key: number]: string } = {}; // Cambia el tipo según sea necesario
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private usermessage: UserMessageService,
    private router: Router,
    private datatransfer: DataService,
  ) { }

  ngOnInit(): void {
    this.getPedidosNoFinalizados();
  }

   getPedidosNoFinalizados() {
     this.loadingTable = true;
     this.requestService.get(Constantes.apiGetPedidosNoFinalizados)
       .pipe(takeUntil(this.destroy$))
       .subscribe({
         next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data.filter((pedido: IestadosPlataforma) => 
            pedido.descripcion_estado !== 'entregado' && 
            pedido.descripcion_estado !== 'entregado con atraso'
          );
        },
         error: () => {
           this.loadingTable = false;
         }
       })
   }

  

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
