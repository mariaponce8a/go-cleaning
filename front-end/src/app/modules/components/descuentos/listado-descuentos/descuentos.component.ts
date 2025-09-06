import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  ITitulosTabla,
  IaccionBotones,
  IdescuentosPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { FormDescuentosComponent } from '../form-descuentos/form-descuentos.component';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';


@Component({
  selector: 'app-listado-descuentos',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent , CommonModule,
        IonicModule
  ],
  templateUrl: './descuentos.component.html',
  styleUrl: './descuentos.component.css',
})
export class ListadoDescuentosComponent implements OnInit, OnDestroy {
  titulosTabla: ITitulosTabla[] = [

    {
      value: 'tipo_descuento_desc',
      viewValue: 'Descripción Tipo Descuento',
    },
    {
      value: 'cantidad_descuento',
      viewValue: 'Cantidad Descuento',
    },
  ];

  valoresDeTabla: IdescuentosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog) {}

  ngOnInit(): void {
    this.getAllDiscounts();  
  }

  getAllDiscounts() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetAllDescuentos)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;
          let arrayAjustado: any[] = [];
          for (let item of this.valoresDeTabla) {
            let body = item;
            arrayAjustado.push(body);
          }
          this.valoresDeTabla = arrayAjustado;
        },
        error: () => {
          this.loadingTable = false;
        }
      });
  }
  
  manejarEventosBotones(evento: IaccionBotones) {
    console.log(evento);
    let dialogRef;
    switch (evento.tipo) {
      case 'editar':
        dialogRef = this.dialog.open(FormDescuentosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        });
  
        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllDiscounts();
          }
        });
  
        break;
      case 'crear':
        dialogRef = this.dialog.open(FormDescuentosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        });
  
        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllDiscounts();
          }
        });
  
        break;
     
        case 'eliminar':
          let body = {
            id_tipo_descuento: evento.fila.id_tipo_descuento
          }
          this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
            if (r.isConfirmed) {
              this.requestService.put(body, Constantes.apiDeleteDescuentos)
                .pipe(takeUntil(this.destroy$))
                .subscribe({
                  next: (value) => {
                    this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                    this.getAllDiscounts();
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