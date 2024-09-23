
import { Component, EventEmitter, Input, OnInit, Output, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { IaccionBotones, IestadosPlataforma, ITitulosTabla } from '../../interface/datamodels.interface';
import { MatButtonModule } from '@angular/material/button';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatFormFieldModule } from '@angular/material/form-field';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { HttpClientModule } from '@angular/common/http';
import { MaterialModule } from '../../../desginModules/material.module';
import { GlobalButtonsComponent } from '../global-buttons/global-buttons.component';
import { ImageDialogComponent } from '../../../../modules/components/imagenes/image-dialog.component';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../services/user-message.service';
import { Router } from '@angular/router';
import { RequestService } from '../../../shared/services/request.service';
import { TablasHomeComponent } from '../../../../modules/components/home/tablas-home/tablas-home.component';

@Component({
  selector: 'app-registros-paginados',
  templateUrl: './registros-paginados.component.html',
  styleUrls: ['./registros-paginados.component.css'],
  standalone: true,
  imports: [
    MaterialModule,
    GlobalButtonsComponent,
    HttpClientModule,
    CommonModule
  ]
})
export class RegistrosPaginadosComponent implements OnInit {
  public dataSource: MatTableDataSource<any[]> = new MatTableDataSource();
  public showZoomButton: boolean = false;
  public rowClasses: { [key: number]: string } = {}; // Guardar las clases
  // En el componente
  compareDates(fechaEntregaEstimada: string): boolean {
    const fechaEntregaEstimadaDate = new Date(fechaEntregaEstimada);
    const currentDate = new Date();
    return fechaEntregaEstimadaDate < currentDate;
  }


  imageBase64: string | null = null;
  @ViewChild(MatPaginator, { static: true }) paginator!: MatPaginator;
  @Input() isLoadingTable!: boolean;
  @Input() titulos!: ITitulosTabla[];
  @Input() valores: any;
  @Output() accionBotones: EventEmitter<IaccionBotones> = new EventEmitter();
  @Input() verFacturas: boolean = false;
  @Input() verEliminar: boolean = true;
  @Input() verEditar: boolean = true;
  @Input() verBtnCrear: boolean = true;
  @Input() verPdf: boolean = false;
  displayColumns: Array<string> = [];

  constructor(
    private usermessage: UserMessageService,
    private dialog: MatDialog,
    private router: Router,
    private requestService: RequestService,


  ) { }
  public mostrarFiltro: boolean = false;

  ngOnInit(): void {
    this.showActions = this.router.url !== '/bds/home'; // Ocultar botones de acción en home
    this.showPdfButtonNextToCreate = this.router.url == '/bds/clientes';
    this.onFillData();

  }

  isEstadosRoute(): boolean {
    return this.router.url.includes('/bds/estados');
  }

  actionRow(type: string, rowData: any) {
    let data: IaccionBotones = {
      tipo: type,
      fila: rowData
    }
    this.accionBotones.emit(data);
  }

  ngOnChanges() {
    this.onFillData();
  }

  onFillData(): void {
    if (this.valores != null) {
      this.displayColumns = [];
      for (let title of this.titulos) {
        this.displayColumns.push(title.value);
      }
      // Solo agrega 'accion' si showActions es true
      if (this.showActions) {
        this.displayColumns.push('accion');
      }
      this.dataSource.data = this.valores;
    }
  }

  openImageDialog(imageUrl: string): void {
    this.dialog.open(ImageDialogComponent, {
      data: { imageUrl, width: '80%', height: '80%' },
      panelClass: 'custom-dialog-container'
    });
  }
  // Método para aplicar el filtro de búsqueda
  applyFilter(event: Event) {
    const filterValue = (event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();  // Aplica el filtro en minúsculas
  }

  public showPdfButtonNextToCreate: boolean = false;
  public showCreateButton: boolean = false;
  public showActions: boolean = false;

  // Método para alternar la visibilidad del filtro
  toggleFiltro() {
    this.mostrarFiltro = !this.mostrarFiltro; // Cambia el estado de la bandera
  }
}