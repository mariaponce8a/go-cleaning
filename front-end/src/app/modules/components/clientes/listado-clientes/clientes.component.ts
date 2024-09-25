import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import {IaccionBotones, ITitulosTabla,IclientesPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { Router } from '@angular/router';
import { MaterialModule } from '../../../desginModules/material.module';
import { MatDialog } from '@angular/material/dialog';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { FormClientesComponent } from '../form-clientes/form-clientes.component'; 
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';


@Component({
  selector: 'app-listado-clientes',
  standalone: true,
  imports: [ MaterialModule,RegistrosPaginadosComponent, ColoredBodyHeaderComponent],
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

  constructor(private requestService: RequestService,
    private router: Router,
    private usermessage: UserMessageService,
    private dialog: MatDialog) {}

  ngOnInit(): void {
    this.getAllClientes();
  }

  getAllClientes() {
    this.loadingTable = true;
    this.requestService
      .get(Constantes.apiGetAllClientes)  
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;

          let arrayAjustado: IclientesPlataforma[] = [];
          for (let item of this.valoresDeTabla) {
            let body = item;
            arrayAjustado.push(body);
          }
          this.valoresDeTabla = arrayAjustado;
        },
        error: () => {
          this.loadingTable = false;
        },
      });
  }

  manejarEventosBotones(evento: IaccionBotones) {
    console.log(evento);
    let dialogRef;
    switch (evento.tipo) {
      case 'editar':
        dialogRef = this.dialog.open(FormClientesComponent,{
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllClientes();
          }
        })

        break;
      case 'crear':
        dialogRef = this.dialog.open(FormClientesComponent, {
          data: evento,
          width: '600px',
          disableClose: true,
        })

        dialogRef.afterClosed().subscribe((r) => {
          if (r == 'ok') {
            this.getAllClientes();
          }
        })

        break;

      case 'eliminar':
        let body = {
          id_cliente: evento.fila.id_cliente
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, Constantes.apiDeleteCliente)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllClientes();
                },
                error: (error) => {
                  this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
                }
              })
          }
        })
        break;
        case 'PDF':
          try {
            this.generateReport();
            console.log('PDF generado exitosamente.');
          } catch (error) {
            console.error('Error al generar el PDF:', error);
            alert('No se pudo generar el PDF. Por favor, intenta nuevamente.');
          }
          break;
        }

  }
  
  generateReport() {
    this.requestService.get(Constantes.apiClientsReport).subscribe(data => {
        const doc = new jsPDF();

        doc.setFont('Helvetica');
        doc.setFontSize(16);

        // Encabezado de la lavandería
        doc.text('Lavandería Burbuja de Seda', 105, 10, { align: 'center' });
        doc.setFontSize(12);
        doc.text('Dirección Matriz: Leonardo Murialdo N57-199 y Miguel Valdiviezo', 105, 18, { align: 'center' });
        doc.text('Kennedy, Quito - Pichincha, Ecuador', 105, 26, { align: 'center' });
        doc.text('Teléfono: 0985369007', 105, 34, { align: 'center' });

        // Título del reporte
        doc.setFontSize(14);
        doc.text('Reporte de Clientes', 105, 50, { align: 'center' });
        doc.setFontSize(12);

        let y = 65; // Posición inicial para el contenido

        if (data && data.respuesta === "1" && data.data.length > 0) {

            const filas = data.data.map((detalle: { identificacion_cliente: string, nombre_completo: string, total_pedidos: number }) => [
                detalle.identificacion_cliente,
                detalle.nombre_completo,
                detalle.total_pedidos,  // Formato de dos decimales para los totales
            ]);

            // Generar la tabla con autoTable
            autoTable(doc, {
                head: [['Identificación', 'Cliente', 'Total de pedidos']],
                body: filas,
                startY: y,
                theme: 'striped',  // Tema elegante para la tabla
                styles: { font: 'Helvetica', fontSize: 10 },
                headStyles: { fillColor: [41, 128, 185], textColor: [255, 255, 255] }, // Azul para el encabezado
                alternateRowStyles: { fillColor: [245, 245, 245] },  // Alterna el color de las filas
                columnStyles: {
                    0: { halign: 'left' },    // Alineación izquierda para Identificación
                    1: { halign: 'center' },  // Alineación centrada para Cliente
                    2: { halign: 'right' }    // Alineación derecha para Total de pedidos
                },
            });

            // Posicionar el cursor después de la tabla
            y = (doc as any).lastAutoTable.finalY + 10;
        } else {
            // Mostrar un mensaje en caso de que no haya datos
            doc.text('No se encontraron clientes con estado de facturación 1.', 105, y, { align: 'center' });
        }

        // Crear y abrir el PDF
        const pdfBlob = doc.output('blob');
        const url = URL.createObjectURL(pdfBlob);
        window.open(url);
    });
}



  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}