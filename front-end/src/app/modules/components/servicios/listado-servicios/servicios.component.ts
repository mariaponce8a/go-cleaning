import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {
  ITitulosTabla,
  IserviciosPlataforma,
} from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listado-servicios',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
  templateUrl: './servicios.component.html',
  styleUrl: './servicios.component.css',
})
export class ListadoServiciosComponent implements OnInit, OnDestroy {
  //   {
  //     "id_servicio": "2",
  //     "descripcion_servicio": "lavar ropa",
  //     "costo_unitario": "12.00",
  //     "validar_pesaje": "1"
  // }
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
  ];

  valoresDeTabla: IserviciosPlataforma[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(private requestService: RequestService, private router: Router) {}

  ngOnInit(): void {
    this.getAllServices();
  }

  getAllServices() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllServices)
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
            arrayAjustado.push(body);
          }
          this.valoresDeTabla = arrayAjustado;
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
