

import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { ITitulosTabla, IAsignacionEmpleadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-asignacion-empleado',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './asignaciones.component.html',
  styleUrls: ['./asignaciones.component.css'],
})
export class ListadoAsignacionEmpleadoComponent  implements OnInit, OnDestroy {

  titulosTabla: ITitulosTabla[] = [
    {
      value: 'id_usuario',
      viewValue: 'ID Usuario',
    },
    {
      value: 'fecha_hora_inicio_asignacion',
      viewValue: 'Fecha Inicio',
    },
    {
      value: 'fecha_hora_fin_asignacion',
      viewValue: 'Fecha Fin',
    },
    {
      value: 'id_pedido',
      viewValue: 'ID Pedido',
    },
    {
      value: 'id_estado',
      viewValue: 'ID Estado',
    },
  ];
  valoresDeTabla: IAsignacionEmpleadosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService, private router: Router) {}

  ngOnInit(): void {
    this.getAllAsignaciones();
  }

  

  getAllAsignaciones() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllAsignaciones)  // Asegúrate de que la URL esté correctamente definida en Constantes
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;
          // Si necesitas transformar algún dato, hazlo aquí
        },
        error: () => {
          this.loadingTable = false;
        },
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}