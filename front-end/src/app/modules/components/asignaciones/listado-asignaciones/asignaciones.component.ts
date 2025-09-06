

import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { IaccionBotones, ITitulosTabla, IAsignacionEmpleadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
import { FormAsignacionesComponent } from '../form-asignaciones/form-asignaciones.component';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-listado-asignaciones',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent, CommonModule,
      IonicModule],
  templateUrl: './asignaciones.component.html',
  styleUrls: ['./asignaciones.component.css'],
})
export class ListadoAsignacionEmpleadoComponent  implements OnInit, OnDestroy {

  titulosTabla: ITitulosTabla[] = [
    {
      value: 'usuario',
      viewValue: 'Empleado',
    },
    {
      value: 'fecha_inicio',
      viewValue: 'Fecha Inicio',
    },
    {
      value: 'fecha_fin',
      viewValue: 'Fecha Fin',
    },
    {
      value: 'id_pedido_cabecera',
      viewValue: 'Ref. Pedido',
    },
    {
      value: 'identificacion_cliente',
      viewValue: 'CI',
    },{
      value: 'nombre_cliente',
      viewValue: 'Nombre',
    },
    {
      value: 'apellido_cliente',
      viewValue: 'Apellido',
    },
    {
      value: 'descripcion_servicio',
      viewValue: 'Servicio',
    },{
      value: 'cantidad_articulos',
      viewValue: 'Num. Artículos',
    },
    {
      value: 'descripcion_articulo',
      viewValue: 'Descripción',
    },
    {
      value: 'libras',
      viewValue: 'Libras',
    },
    {
      value: 'descripcion_estado',
      viewValue: 'Estado',
    },
  ];
  valoresDeTabla: IAsignacionEmpleadosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

 

  constructor(
    private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog
  ) { }
  ngOnInit(): void {
    this.titulosTabla = this.titulosTabla.map(item => {
          if (item.value === 'id_pedido_cabecera') {
            return { ...item, hidden: true };
          } else {
            return item;
          }
        });
    this.getAllAsignaciones();
  }


  getAllAsignaciones() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllAsignaciones) 
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;

         
        },
        error: () => {
          this.loadingTable = false;
        },
      });
  }

  manejarEventosBotones(evento: IaccionBotones) {
    console.log('Evento recibido:', evento); 
    let dialogRef;
    switch (evento.tipo) {
      case 'editar':
        dialogRef = this.dialog.open(FormAsignacionesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllAsignaciones();
          }
        })

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormAsignacionesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllAsignaciones();
          }
        })

        break;

      case 'eliminar':
        let body = {
          id_asignaciones: evento.fila.id_asignaciones
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteAsignaciones)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllAsignaciones();
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