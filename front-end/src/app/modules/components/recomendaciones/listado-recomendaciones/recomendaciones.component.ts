import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { IaccionBotones, ITitulosTabla, IrecomendacionesPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

import { FormRecomendacionesComponent } from '../form-recomendaciones/form-recomendaciones.component';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';

@Component({
  selector: 'app-listado-recomendaciones',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './recomendaciones.component.html',
  styleUrls: ['./recomendaciones.component.css'],
})
export class ListadoRecomendacionesComponent implements OnInit, OnDestroy {
  titulosTabla: ITitulosTabla[] = [
    
    {
      value: 'descripcion_material',
      viewValue: 'Material',
    },
    {
      value: 'descripcion_servicio',  
      viewValue: 'Servicio',
    },
  ];

  valoresDeTabla: IrecomendacionesPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog
  ) { }
  ngOnInit(): void {
    this.getAllRecommendations();
  }

  getAllRecommendations() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllRecommendations)
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
        dialogRef = this.dialog.open(FormRecomendacionesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllRecommendations();
          }
        })

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormRecomendacionesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllRecommendations();
          }
        })

        break;

      case 'eliminar':
        let body = {
          id_recomendacion_lavado: evento.fila.id_recomendacion_lavado
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteRecomendaciones)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllRecommendations();
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
