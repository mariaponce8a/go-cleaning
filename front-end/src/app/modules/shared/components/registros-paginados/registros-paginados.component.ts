
import { Component, EventEmitter, Input, OnInit, Output, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { ITitulosTabla } from '../../interface/datamodels.interface';
import { MatButtonModule } from '@angular/material/button';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatFormFieldModule } from '@angular/material/form-field';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { HttpClientModule } from '@angular/common/http';


@Component({
  selector: 'app-registros-paginados',
  templateUrl: './registros-paginados.component.html',
  styleUrls: ['./registros-paginados.component.css'],
  standalone: true,
  imports: [
    MatTableModule,
    MatButtonModule,
    MatTooltipModule,
    MatFormFieldModule,
    FormsModule,
    MatProgressBarModule,
    ReactiveFormsModule,
    MatIconModule,
    HttpClientModule,
    CommonModule
  ]
})
export class RegistrosPaginadosComponent implements OnInit {
  public dataSource: MatTableDataSource<any[]> = new MatTableDataSource();
  @ViewChild(MatPaginator, { static: true }) paginator!: MatPaginator;

  @Input() isLoadingTable!: boolean;
  @Input() titulos!: ITitulosTabla[];
  @Input() valores: any;
  displayColumns: Array<string> = [];

  ngOnInit(): void {

    this.onFillData();
  }

  actionRow(type: string, rowData: any) {

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
      this.displayColumns.push('accion');
      this.dataSource.data = this.valores;
    }
  }


}
