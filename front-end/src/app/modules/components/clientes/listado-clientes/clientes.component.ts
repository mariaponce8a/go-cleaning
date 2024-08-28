import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { ITitulosTabla,IclientesPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';

@Component({
  selector: 'app-listado-clientes',
  standalone: true,
  imports: [RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
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

  constructor(private requestService: RequestService, private router: Router) {}

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

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}