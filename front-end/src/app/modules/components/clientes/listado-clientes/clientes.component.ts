import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {IaccionBotones, ITitulosTabla,IclientesPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { FormClientesComponent } from '../form-clientes/form-clientes.component'; 


@Component({
  selector: 'app-listado-clientes',
  standalone: true,
  imports: [ MaterialModule,RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './clientes.component.html',
  styleUrl: './clientes.component.css',
})
export class ListadoClientesComponent implements OnInit, OnDestroy {
  
  titulosTabla: ITitulosTabla[] = [
    {
      value: 'identificacion_cliente',
      viewValue: 'Identificación',
    },
    {
      value: 'tipo_identificacion_cliente',
      viewValue: 'Tipo de Identificación',
    },
    {
      value: 'nombre_cliente',
      viewValue: 'Nombre',
    },
    {
      value: 'apellido_cliente',
      viewValue: 'Apellido',
    },
    {
      value: 'telefono_cliente',
      viewValue: 'Teléfono',
    },
    {
      value: 'correo_cliente',
      viewValue: 'Correo Electrónico',
    },
  ];
  valoresDeTabla: IclientesPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog) {}

  ngOnInit(): void {
    this.getAllClientes();
  }

  getAllClientes() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllClientes)  // Asegúrate de que la URL esté correctamente definida en Constantes
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;

          // Si necesitas transformar algún dato, hazlo aquí.
          let arrayAjustado: IclientesPlataforma[] = [];
          for (let item of this.valoresDeTabla) {
            let body = item;
            // Puedes realizar transformaciones aquí si es necesario.
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
        dialogRef = this.dialog.open(FormClientesComponent,{
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllClientes();
          }
        })

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormClientesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllClientes();
          }
        })

        break;

      case 'eliminar':
        let body = {
          id_cliente: evento.fila.id_cliente
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteCliente)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllClientes();
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