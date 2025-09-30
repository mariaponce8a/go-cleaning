import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common'; // ← Agregar esta importación
import { EstadisticasService } from '../../shared/services/estadisticas.service';
import { ColoredBodyHeaderComponent } from '../../shared/components/colored-body-header/colored-body-header.component';
@Component({
  selector: 'app-estadisticas',
  templateUrl: './estadisticas.component.html',
  standalone: true, 
  styleUrls: ['./estadisticas.component.css'],
  imports: [
    CommonModule,
  ColoredBodyHeaderComponent,
] 
})
export class EstadisticasComponent implements OnInit {
  periodoSeleccionado: string = 'dia';
  loading: boolean = false;
  
  // Datos de estadísticas
  estadisticasGenerales: any = {};
  serviciosMasSolicitados: any[] = [];
  topClientes: any[] = [];
  controlCaja: any = {};
  
  periodos = [
    { valor: 'dia', texto: 'Hoy' },
    { valor: 'semana', texto: 'Esta Semana' },
    { valor: 'mes', texto: 'Este Mes' },
    { valor: 'año', texto: 'Este Año' }
  ];

  constructor(private estadisticasService: EstadisticasService) { }

  ngOnInit() {
    this.cargarTodasEstadisticas();
  }

  cambiarPeriodo(periodo: string) {
    this.periodoSeleccionado = periodo;
    this.cargarTodasEstadisticas();
  }

 cargarTodasEstadisticas() {
  this.loading = true;
  
  console.log('🔄 Iniciando carga de estadísticas...');
  console.log('📡 Período seleccionado:', this.periodoSeleccionado);
  
  this.estadisticasService.getAllEstadisticas(this.periodoSeleccionado).subscribe({
    next: (response: any) => {
      console.log('✅ Respuesta completa del API:', response);
      
      if (response.respuesta === '1') {
        const data = response.data;
        console.log('📊 Data recibida:', data);
        
        // CORRECCIÓN: Ahora servicioMasSolicitado es un array
        this.estadisticasGenerales = data.estadisticasGenerales || {};
        this.serviciosMasSolicitados = data.servicioMasSolicitado || []; // ← Array, no objeto
        this.topClientes = data.topClientes || [];
        this.controlCaja = data.controlCaja || {};
        
        console.log('🔍 Estadísticas generales:', this.estadisticasGenerales);
        console.log('🔍 Servicios más solicitados:', this.serviciosMasSolicitados);
        console.log('🔍 Top clientes:', this.topClientes);
        console.log('🔍 Control caja:', this.controlCaja);
        
        // Mostrar el servicio más solicitado (primer elemento del array)
        if (this.serviciosMasSolicitados.length > 0) {
          console.log('🏆 Servicio MÁS solicitado:', this.serviciosMasSolicitados[0]);
        }
      } else {
        console.error('❌ Error en respuesta:', response.mensaje);
      }
      this.loading = false;
    },
    error: (error) => {
      console.error('💥 Error en petición:', error);
      console.error('💥 Error details:', error.error);
      this.loading = false;
    },
    complete: () => {
      console.log('🏁 Carga de estadísticas completada');
    }
  });
}

  formatearMoneda(valor: number): string {
    if (!valor) return '$0.00';
    return new Intl.NumberFormat('es-ES', {
      style: 'currency',
      currency: 'USD'
    }).format(valor);
  }

  formatearNumero(valor: number): string {
    if (!valor) return '0';
    return new Intl.NumberFormat('es-ES').format(valor);
  }

  getNombreMes(mes: number): string {
    const meses = [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    return meses[mes - 1] || '';
  }

  getPeriodoTexto(): string {
  const periodo = this.periodos.find(p => p.valor === this.periodoSeleccionado);
  return periodo ? periodo.texto : 'Período no definido';
}

getFechaActual(): string {
  return new Date().toLocaleString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}
}