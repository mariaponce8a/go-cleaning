import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  ITitulosTabla,
  IRecomendacionesPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

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
      value: 'id_recomendacion',
      viewValue: 'ID Recomendación',
    },
    {
      value: ' id_material',
      viewValue: 'ID Material',
    },
    {
      value: 'id_servicio',  
      viewValue: 'ID Servicio',
    },
  ];

  valoresDeTabla: IRecomendacionesPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService, private router: Router) {}

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

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
