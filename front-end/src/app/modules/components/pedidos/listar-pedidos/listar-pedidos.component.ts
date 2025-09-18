import { Component, OnDestroy, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { ITitulosTabla, IaccionBotones, IpedidosJoin } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { Router } from '@angular/router';
import { FormularioPedidosComponent } from '../formulario-pedido/formulario-pedidos.component';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';



@Component({
  selector: 'app-listar-pedidos',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent,
    FormularioPedidosComponent, CommonModule,
        IonicModule
  ],
  templateUrl: './listar-pedidos.component.html',
  styleUrl: './listar-pedidos.component.css'
})
export class ListarPedidosComponent implements OnInit, OnDestroy {

  titulosTabla: ITitulosTabla[] = [
    {
      value: "fecha_pedido",
      viewValue: "Fecha pedido"
    }, 
    {
      value: "usuario",
      viewValue: "Empleado"
    },
    {
      value: "cantidad_articulos",
      viewValue: "#Artículos"
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
    }, 
    {
      value: "descripcion_estado",
      viewValue: "Estado"
    },
    {
      value: "total",
      viewValue: "Subtotal"
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
    this.getAllPedidos();
  }

  getAllPedidos() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetAllPedidos)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data;

        },
        error: () => {
          this.loadingTable = false;
        }
      })
  }

  manejarEventosBotones(evento: IaccionBotones) {
    console.log(evento); // Agrega este log para verificar la estructura del evento

    switch (evento.tipo) {
      case 'editar':
        this.datatransfer.setDatos(evento);
        this.router.navigateByUrl('/bds/formulario-pedido');
        break;
      case 'crear':
        this.datatransfer.setDatos(evento);
        this.router.navigateByUrl('/bds/formulario-pedido');
        break;
      case 'eliminar':
        let body = {
          id: evento.fila.id_usuario
        }
        this.usermessage.questionMessage(Constantes.deleteQuestion).then((r) => {
          if (r.isConfirmed) {
            this.requestService.put(body, 'insertar api eliminar')
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (value) => {
                  this.usermessage.getToastMessage('success', Constantes.deleteResponseMsg).fire();
                  this.getAllPedidos();
                },
                error: (error) => {
                  this.usermessage.getToastMessage('error', Constantes.errorResponseMsg).fire();
                }
              })
          }
        })
        break;
        case 'PDF':
          if (evento.fila && evento.fila.id_pedido_cabecera) {
            this.generatePDF(evento.fila.id_pedido_cabecera);
        } else {
            console.error('El evento no tiene la estructura esperada', evento);
        }
        break;
      }

  }
  
  generatePDF(id_pedido_cabecera: number) {
    if (!id_pedido_cabecera) {
        console.error('ID de pedido no proporcionado');
        return;
    }

    this.requestService.get(`${Constantes.apiOrdenPedido}/${id_pedido_cabecera}`).subscribe(data => {
        const doc = new jsPDF();
        doc.setFont('Courier');
        doc.setFontSize(16);

        // Encabezado de la lavandería
        doc.text('Lavandería GO CLEANING', 105, 10, { align: 'center' });
        doc.setFontSize(12);
        doc.text('Dirección Matriz: Leonardo Murialdo N57-199 y Miguel Valdiviezo', 105, 20, { align: 'center' });
        doc.text('Kennedy, Quito - Pichincha, Ecuador', 105, 30, { align: 'center' });
        doc.text('Teléfono: 0985369007', 105, 40, { align: 'center' });

        // Texto "Orden de Pedido"
        doc.setFontSize(14);
        doc.text('Orden de Pedido', 10, 55);
        doc.setFontSize(12);

        const margin = 10;
        const contentWidth = 190; // Ancho máximo del contenido
        let y = 70; // Posición inicial del contenido

        if (data && data.respuesta === "1" && data.data.length > 0) {
            const pedido = data.data[0];

            const fields = [
                `Fecha de Pedido: ${pedido.fecha_pedido || 'N/A'}`,
                `Usuario: ${pedido.nombre_usuario_completo || 'N/A'}`,
                `Cliente: ${pedido.nombre_cliente_completo || 'N/A'} (${pedido.identificacion_cliente || 'N/A'})`,
                `Estado de Pago: ${pedido.estado_pago || 'N/A'}`,
                `Tipo de Entrega: ${pedido.tipo_entrega || 'N/A'}`,
                `Fecha Recolección Estimada: ${pedido.fecha_recoleccion_estimada || 'N/A'}`,
                `Hora Recolección Estimada: ${pedido.hora_recoleccion_estimada || 'N/A'}`,
                `Fecha Entrega Estimada: ${pedido.fecha_entrega_estimada || 'N/A'}`,
                `Dirección de Recolección: ${pedido.direccion_recoleccion || 'N/A'}`,
                `Dirección de Entrega: ${pedido.direccion_entrega || 'N/A'}`
            ];

            for (const field of fields) {
                const splitText = doc.splitTextToSize(field, contentWidth);
                for (const line of splitText) {
                    doc.text(line, margin, y);
                    y += 5;
                    if (y > 270) {
                        doc.addPage();
                        y = 10;
                    }
                }
            }

            y += 5; // Espacio antes de la tabla

            // Crear la tabla de detalles
            const filas = data.data.map((detalle: { descripcion_servicio: string, descripcion_articulo: string, cantidad: number, libras: number, precio_servicio: number }) => ([
                detalle.descripcion_servicio || 'N/A',
                detalle.descripcion_articulo || 'N/A',
                detalle.cantidad ? detalle.cantidad : 'N/A',
                detalle.libras ? detalle.libras : 'N/A',
                detalle.precio_servicio ? `$${detalle.precio_servicio}` : 'N/A'
            ]));

            autoTable(doc, {
                head: [['Servicio', 'Descripción', '#Artículos', 'Libras', 'Precio']],
                body: filas,
                startY: y,
                theme: 'grid',
                styles: { font: 'Courier', fontSize: 12 },
                headStyles: { fillColor: [200, 200, 200] },
                columnStyles: { 0: { halign: 'left' }, 1: { halign: 'center' }, 2: { halign: 'right' } },
            });

            // Ajustar `y` después de la tabla
            y = (doc as any).lastAutoTable.finalY + 10;

            doc.text(`Subtotal: $${pedido.pedido_subtotal ? pedido.pedido_subtotal : 'N/A'}`, margin, y);
            doc.text(`Tipo de Descuento: ${pedido.tipo_descuento_desc || 'N/A'}`, margin, y += 5);
            doc.text(`Valor Pagado: $${pedido.valor_pago ? pedido.valor_pago : 'N/A'}`, margin, y += 5);

            // Agregar detalles del pedido en páginas adicionales
            for (const detalle of data.data) {
                doc.addPage();
                doc.text('Lavandería GO CLEANING', 105, 10, { align: 'center' });
                doc.setFontSize(12);
                doc.text('Dirección Matriz: Leonardo Murialdo N57-199 y Miguel Valdiviezo', 105, 20, { align: 'center' });
                doc.text('Kennedy, Quito - Pichincha, Ecuador', 105, 30, { align: 'center' });
                doc.text('Teléfono: 0985369007', 105, 40, { align: 'center' });

                doc.setFontSize(14);
                doc.text('Detalle de Servicio', 10, 55);
                doc.setFontSize(12);

                let detalleY = 70; // Inicializar una nueva posición para los detalles
                
                doc.text(`#Detalle de pedido: ${detalle.id_pedido_detalle || 'N/A'}`, margin, detalleY);
                detalleY += 10; 
                doc.text(`Fecha de Pedido: ${detalle.fecha_pedido || 'N/A'}`, margin, detalleY);
                detalleY += 10;
                doc.text(`Usuario: ${detalle.nombre_usuario_completo || 'N/A'}`, margin, detalleY);
                detalleY += 10;
                doc.text(`Cliente: ${detalle.nombre_cliente_completo || 'N/A'} (${pedido.identificacion_cliente || 'N/A'})`, margin, detalleY);
                detalleY += 10;
                
                // Manejar la descripción del artículo para ajustar el texto largo
                const descripcionArticulo = `Descripción: ${detalle.descripcion_articulo || 'N/A'}`;
                const splitText = doc.splitTextToSize(descripcionArticulo, contentWidth);
                for (const line of splitText) {
                    doc.text(line, margin, detalleY);
                    detalleY += 10; // Incrementar la posición para la siguiente línea
                }
          
                doc.text(`Número de Artículos: ${detalle.cantidad ? detalle.cantidad : 'N/A'}`, margin, detalleY);
                detalleY += 10;
                doc.text(`Libras: ${detalle.libras ? detalle.libras : 'N/A'}`, margin, detalleY);
                detalleY += 10;
                doc.text(`Precio: $${detalle.precio_servicio ? detalle.precio_servicio : 'N/A'}`, margin, detalleY);
                detalleY += 10;
                doc.text(`Fecha Entrega Estimada: ${detalle.fecha_entrega_estimada || 'N/A'}`, margin, detalleY);
            }
        } else {
            doc.text('No se encontraron detalles para este pedido.', margin, y += 10);
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
