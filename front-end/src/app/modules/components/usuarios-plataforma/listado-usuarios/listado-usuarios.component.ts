import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { IaccionBotones, ITitulosTabla, IusuariosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

import { FormUsuariosComponent } from '../form-usuarios/form-usuarios.component';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-listado-usuarios',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent,
     CommonModule,
        IonicModule 
  ],
  templateUrl: './listado-usuarios.component.html',
  styleUrl: './listado-usuarios.component.css'
})
export class ListadoUsuariosComponent implements OnInit, OnDestroy {

  titulosTabla: ITitulosTabla[] = [
    {
      value: "usuario",
      viewValue: "Usuario"
    },
    {
      value: "nombre",
      viewValue: "Nombre"
    },
    {
      value: "apellido",
      viewValue: "Apellido"
    },
    {
      value: "email",
      viewValue: "Email"
    },
    {
      value: "perfil",
      viewValue: "Perfil"
    },
  ]

  valoresDeTabla: IusuariosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog) {}
  ngOnInit(): void {
    this.getAllUsers();
  }

  getAllUsers() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetAllUsers)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;
          let arrayAjustado: IusuariosPlataforma[] = [];
          for (let item of this.valoresDeTabla) {
            let body = item;
            if (body.perfil == 'E') {
              body.perfil = 'Empleado';
            } else {   
              body.perfil = 'Administrador';
            }
            arrayAjustado.push(body);
          }
          this.valoresDeTabla = arrayAjustado;
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
        dialogRef = this.dialog.open(FormUsuariosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllUsers();
          }
        })

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormUsuariosComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllUsers();
          }
        })

        break;
     
      case 'eliminar':
        let body = {
          id: evento.fila.id_usuario
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteUser)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllUsers();
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
