import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  ITitulosTabla,
  IEstadosPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listado-estados',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './estados.component.html',
  styleUrl: './estados.component.css',
})
export class ListadoEstadosComponent implements OnInit, OnDestroy {
  titulosTabla: ITitulosTabla[] = [
    
    {
      value: 'descripcion_estado',
      viewValue: 'Descripción Estado',
    },
  ];

  valoresDeTabla: IEstadosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService, private router: Router) {}

  ngOnInit(): void {
    this.getAllEstados();
  }

  getAllEstados() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllEstados)
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

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
