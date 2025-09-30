import { Component, OnDestroy, OnInit } from '@angular/core';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { ITitulosTabla, IpedidosJoin, IaccionBotones } from '../../../shared/interface/datamodels.interface';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { RequestService } from '../../../shared/services/request.service';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { Router } from '@angular/router';
import { FacturasPreparadasComponent } from '../facturas-preparadas/facturas-preparadas.component';
import { ModalHeaderComponent } from '../../../shared/components/modal-header/modal-header.component';
import { GlobalButtonsComponent } from '../../../shared/components/global-buttons/global-buttons.component';
import { CommonModule } from '@angular/common';
import { IonicModule, isPlatform } from '@ionic/angular';
import { FileOpener } from '@capacitor-community/file-opener';
import { Filesystem, Directory } from '@capacitor/filesystem';

@Component({
  selector: 'app-listado-pedidos-facturar',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    FacturasPreparadasComponent,
    ColoredBodyHeaderComponent,
    ModalHeaderComponent,
    GlobalButtonsComponent,
    CommonModule,
    IonicModule
  ],
  templateUrl: './listado-pedidos-facturar.component.html',
  styleUrl: './listado-pedidos-facturar.component.css'
})
export class ListadoPedidosFacturarComponent implements OnInit, OnDestroy {
  dataDelPedidoDetalle: any;


  titulosTabla: ITitulosTabla[] = [
    {
      value: "fecha_pedido",
      viewValue: "Fecha pedido"
    },
    {
      value: "identificacion_cliente",
      viewValue: "ID:Cliente"
    },
    {
      value: "nombre_cliente",
      viewValue: "Nombre cliente"
    },
    {
      value: "apellido_cliente",
      viewValue: "Apellido cliente"
    }
  ]

  valoresDeTabla: IpedidosJoin[] = [];
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  constructor(
    private requestService: RequestService,
    private usermessage: UserMessageService,
    private router: Router,
    private datatransfer: DataService,
  ) { }


  ngOnInit(): void {
    this.getAllFacturas();
  }

  getAllFacturas() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiPedidosNoCancelados)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;
          this.dataDelPedidoDetalle = value.data.detalle;

        },
        error: () => {
          this.loadingTable = false;
        }
      })
  }

  manejarEventosBotones(evento: IaccionBotones) {
    switch (evento.tipo) {
      case 'recibos':
        console.log(evento);
        this.datatransfer.setDatos(evento);
        this.router.navigateByUrl('/bds/facturas-del-servicio');
        break;
      case 'PDF':
        if (evento.fila && evento.fila.id_pedido_cabecera) {
          this.generarFactura(evento.fila.id_pedido_cabecera);
        } else {
          console.error('El evento no tiene la estructura esperada', evento);
        }
        break;
    }

  }

  async generarFactura(id_pedido_cabecera: number) {
    if (!id_pedido_cabecera) {
      console.error('ID de pedido no proporcionado');
      return;
    }

    this.requestService.get(`${Constantes.apiFactura}/${id_pedido_cabecera}`).subscribe(async data => {
      const doc = new jsPDF();
      doc.setFont('Helvetica');
      doc.setFontSize(16);

      // Encabezado de la lavandería
      doc.text('Lavandería GO CLEANING', 105, 10, { align: 'center' });
      doc.setFontSize(12);
      doc.text('Dirección Matriz: Leonardo Murialdo N57-199 y Miguel Valdiviezo', 105, 20, { align: 'center' });
      doc.text('Kennedy, Quito - Pichincha, Ecuador', 105, 30, { align: 'center' });
      doc.text('Teléfono: 0985369007', 105, 40, { align: 'center' });
      doc.text('RUC: 1721240610001', 105, 50, { align: 'center' });

      const margin = 10; // Margen
      const contentWidth = 190; // Ancho máximo del contenido
      const boxWidth = (doc.internal.pageSize.width - 3 * margin) / 2; // Ancho de los cuadros (ajustar a la mitad)
      let y = 70; // Posición inicial del contenido

      if (data && data.respuesta === "1" && data.data.length > 0) {
        const pedido = data.data[0];

        doc.setFontSize(14);
        doc.text('Factura #000' + pedido.id_pedido_detalle, 10, 55);
        doc.setFontSize(12);

        // Desglose del IVA
        const precioServicio = Number(pedido.precio_servicio) || 0;
        const iva = Number((precioServicio * 0.15).toFixed(2)); // Calcular el IVA y formatear a dos decimales
        const totalConIVA = Number((precioServicio + iva).toFixed(2)); // Calcular el total y formatear

        // Formatear los valores numéricos
        const formatter = new Intl.NumberFormat('es-EC', { style: 'currency', currency: 'USD' });

        // Información del pedido
        const fields = [
          `Fecha de Emisión: ${pedido.fecha_pedido}`,
          `Usuario: ${pedido.nombre_usuario_completo}`,
          `Identificación: ${pedido.identificacion_cliente}`,
          `Cliente: ${pedido.nombre_cliente_completo}`,
          `Teléfono: ${pedido.telefono_cliente}`,
          `Correo: ${pedido.correo_cliente}`,
        ];

        // Crear cuadro para Fecha de Emisión y Usuario
        const userBoxX = margin; // Posición X para el cuadro del usuario
        const userBoxY = y; // Posición Y inicial

        // Dibuja el cuadro del usuario
        doc.setDrawColor(169, 169, 169); // Color gris para las líneas
        doc.rect(userBoxX, userBoxY, boxWidth, 50); // Cuadro del usuario

        // Añadir títulos y valores en el cuadro del usuario
        doc.text('Fecha de Emisión:', userBoxX + 5, userBoxY + 10);
        doc.text(` ${pedido.fecha_pedido}`, userBoxX + 5, userBoxY + 20);
        doc.text('Usuario:', userBoxX + 5, userBoxY + 30);
        doc.text(` ${pedido.nombre_usuario_completo}`, userBoxX + 5, userBoxY + 40);

        // Crear cuadro para Identificación y Cliente
        const clientBoxX = userBoxX + boxWidth + margin; // Posición X para el cuadro del cliente
        const clientBoxY = userBoxY; // Posición Y igual a la del cuadro del usuario

        // Dibuja el cuadro del cliente
        doc.rect(clientBoxX, clientBoxY, boxWidth, 50); // Cuadro del cliente

        // Añadir títulos y valores en el cuadro del cliente
        doc.text(`Identificación: ${pedido.identificacion_cliente}`, clientBoxX + 5, clientBoxY + 10);
        doc.text(`Cliente: ${pedido.nombre_cliente_completo}`, clientBoxX + 5, clientBoxY + 20);
        doc.text(`Teléfono: ${pedido.telefono_cliente}`, clientBoxX + 5, clientBoxY + 30);
        doc.text(`Correo: ${pedido.correo_cliente}`, clientBoxX + 5, clientBoxY + 40);

        y += 60; // Espacio después de los cuadros

        // Crear la tabla de detalles
        const filas = data.data.map((detalle: { descripcion_servicio: string, cantidad: number, libras: number, precio_servicio: number, requierePesaje: boolean }) => {
          // Determina el valor a mostrar basado en si requiere pesaje
          const cantidad = detalle.requierePesaje ? detalle.libras : detalle.cantidad;

          return [
            detalle.descripcion_servicio || 'N/A',
            cantidad || 'N/A',
            detalle.libras || 'N/A',
            formatter.format(detalle.precio_servicio)  // Formatear el precio
          ];
        });

        // Generación de la tabla
        autoTable(doc, {
          head: [['Servicio', 'Artículos', 'Libras', 'Precio']],
          body: filas,
          startY: y,
          theme: 'grid',
          styles: { font: 'Helvetica', fontSize: 12 },
          headStyles: { fillColor: [41, 128, 185], lineColor: [169, 169, 169], textColor: [255, 255, 255] }, // Azul para el encabezado
          columnStyles: { 0: { halign: 'left' }, 1: { halign: 'center' }, 2: { halign: 'right' } },
          margin: { left: margin, right: margin },
        });

        y = (doc as any).lastAutoTable.finalY + 10;

        // Calcular subtotal, IVA y total
        const subtotal = pedido.precio_servicio || 0;

        const totalsBoxY = y; // Ajustar para que el cuadro comience justo después de la tabla
        const totalsBoxX = doc.internal.pageSize.width - boxWidth - margin; // Ubicación X en la parte derecha
        const totalsBoxWidth = boxWidth; // Ancho del cuadro
        const totalsBoxHeight = 60; // Altura del cuadro (ajustar para que quede más corto si es necesario)

        // Dibuja el cuadro de totales
        doc.setDrawColor(169, 169, 169); // Color gris para las líneas
        doc.rect(totalsBoxX, totalsBoxY, totalsBoxWidth, totalsBoxHeight); // Cuadro de totales

        // Mostrar subtotal, IVA y total dentro del cuadro
        let totalY = totalsBoxY + 10; // Espacio inicial dentro del cuadro
        const titleX = totalsBoxX + 5; // Posición X para los títulos
        const valueX = totalsBoxX + totalsBoxWidth - 40; // Posición X para los valores

        // Añadir títulos y valores, alineando a la derecha
        doc.text('Subtotal:', titleX, totalY);
        doc.text(formatter.format(subtotal), valueX, totalY, { align: 'right' });
        totalY += 10;

        doc.text('Descuento:', titleX, totalY);
        doc.text(pedido.tipo_descuento_desc || 'S/D', valueX, totalY, { align: 'right' });
        totalY += 10;

        doc.text('IVA (15%):', titleX, totalY);
        doc.text(formatter.format(iva), valueX, totalY, { align: 'right' });
        totalY += 10;

        doc.text('Total:', titleX, totalY);
        doc.text(formatter.format(totalConIVA), valueX, totalY, { align: 'right' });
      } else {
        doc.text('No se encontraron detalles para este pedido.', margin, y += 10);
      }

      // Crear y abrir el PDF
      // const pdfBlob = doc.output('blob');
      // const url = URL.createObjectURL(pdfBlob);
      // window.open(url);

      //Validar que cuando sea movil se abra en el navegador movil con su metodos
      if (isPlatform('android') || isPlatform('ios')) {
        try {
          const pdfOutput = doc.output('arraybuffer');
          const base64Data = this.arrayBufferToBase64(pdfOutput);
          const result = await Filesystem.writeFile({
            path: `factura.pdf`,
            data: base64Data,
            directory: Directory.Documents,
          });
          await FileOpener.open({
            filePath: result.uri,
            contentType: 'application/pdf',
          });
        } catch (err) {
          console.error('Error al guardar/abrir el PDF:', err);
        }
      } else {
        const pdfBlob = doc.output('blob');
        const url = URL.createObjectURL(pdfBlob);
        window.open(url);
      }
    });
  }

  arrayBufferToBase64(buffer: ArrayBuffer): string {
    let binary = '';
    const bytes = new Uint8Array(buffer);
    const chunkSize = 0x8000; // procesar en bloques para evitar desbordes en PDFs grandes

    for (let i = 0; i < bytes.length; i += chunkSize) {
      const chunk = bytes.subarray(i, i + chunkSize);
      binary += String.fromCharCode.apply(null, chunk as unknown as number[]);
    }

    return btoa(binary);
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}


//1721240610001