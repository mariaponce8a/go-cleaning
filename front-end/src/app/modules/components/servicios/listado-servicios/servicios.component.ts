import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  IaccionBotones,
  ITitulosTabla,
  IserviciosPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

import { FormServiciosComponent } from '../form-servicios/form-servicios.component';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';   
import { UserMessageService } from '../../../shared/services/user-message.service';

@Component({
  selector: 'app-listado-servicios',
  standalone: true,
  imports: [ MaterialModule, RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './servicios.component.html',
  styleUrl: './servicios.component.css',
})
export class ListadoServiciosComponent implements OnInit, OnDestroy {
  
  titulosTabla: ITitulosTabla[] = [
    {
      value: 'descripcion_servicio',
      viewValue: 'Descripción',
    },
    {
      value: 'costo_unitario',
      viewValue: 'Costo Unitario',
    },
    {
      value: 'validar_pesaje',
      viewValue: 'Validar Pesaje',
    },
    {
      value: 'maximo_articulos',
      viewValue: 'Maximo articulos',
    },
  ];

  valoresDeTabla: IserviciosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog
  ) { }
  ngOnInit(): void {
    this.getAllServices();
  }

  getAllServices() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetAllServices)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;
          

          
          let arrayAjustado: IserviciosPlataforma[] = [];
          for (let item of this.valoresDeTabla) {
            let body = item;
            
            if (body.validar_pesaje == 1) {
              body.validar_pesaje = 'Validar';
            } else {
              body.validar_pesaje = 'No validar';
            }
             // Ajustar el campo maximos_articulos
             if (body.maximo_articulos === undefined || body.maximo_articulos === null || body.maximo_articulos === '0') {
              body.maximo_articulos = null; // Asignar nulo si no hay valor
            }
  
            arrayAjustado.push(body);
          }
  
          this.valoresDeTabla = arrayAjustado;
        },
        error: () => {
          this.loadingTable = false;
        },
      });
  }

  manejarEventosBotones(evento: IaccionBotones) {
    console.log(evento);
    let dialogRef;
    switch (evento.tipo) {
      case 'editar':
        dialogRef = this.dialog.open(FormServiciosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        });

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllServices();
          }
        });

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormServiciosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllServices();
          }
        })

        break;
     
      case 'eliminar':
        let body = {
          id_servicio: evento.fila.id_servicio
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteServices)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllServices();
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

