import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { ITitulosTabla, IusuariosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listado-usuarios',
  standalone: true,
  imports: [
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent
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
      value: "perfil",
      viewValue: "Apellido"
    },
  ]

  valoresDeTabla: IusuariosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private router: Router
  ) { }

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
