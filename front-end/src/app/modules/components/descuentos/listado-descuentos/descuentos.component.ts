import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  ITitulosTabla,
  IdescuentosPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listado-descuentos',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './descuentos.component.html',
  styleUrl: './descuentos.component.css',
})
export class ListadoDescuentosComponent implements OnInit, OnDestroy {
  titulosTabla: ITitulosTabla[] = [
    {
      value: 'id_tipo_descuento',
      viewValue: 'ID Tipo Descuento',
    },
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

  constructor(private requestService: RequestService, private router: Router) {}

  ngOnInit(): void {
    this.getAllDiscounts();
  }

  getAllDiscounts() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllDescuentos)
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
