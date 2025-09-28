import { Component, EventEmitter, Output, OnDestroy, Input, OnInit } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { RegistrosPaginadosComponent } from '../../../shared/components/registros-paginados/registros-paginados.component';
import { Subject, takeUntil } from 'rxjs';
import { Constantes } from '../../../config/constantes';
import { ITitulosTabla, IpedidosJoin, IestadosPlataforma, IAsignacionEmpleadosPlataforma } from '../../../shared/interface/datamodels.interface';
import { RequestService } from '../../../shared/services/request.service';
import { UserMessageService } from '../../../shared/services/user-message.service';
import { Router } from '@angular/router';
import { DataService } from '../../../shared/services/dataTransfer.service';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

// NUEVOS IMPORTS para el modal
import { MatDialog } from '@angular/material/dialog';
import { CambiarPasswordObligatorioComponent } from '../../cambiar-password-obligatorio/cambiar-password-obligatorio.component'; // AJUSTA LA RUTA al modal
import { LocalStorageEncryptationService } from '../../../shared/services/local-storage-encryptation.service'; // Para localStorage

@Component({
  selector: 'app-tablas-home',
  standalone: true,
  imports: [
    MaterialModule,
    RegistrosPaginadosComponent,
    ColoredBodyHeaderComponent,
    CommonModule,
    IonicModule
  ],
  templateUrl: './tablas-home.component.html',
  styleUrl: './tablas-home.component.scss' // Cambié a .scss para estilos del overlay
})
export class TablasHomeComponent implements OnInit, OnDestroy {
  // Tus propiedades originales
  titulosTabla: ITitulosTabla[] = [
    { value: "fecha_pedido", viewValue: "Fecha pedido" },
    { value: "nombre_usuario_completo", viewValue: "Empleado" },
    { value: "cantidad_articulos", viewValue: "# Artículos" },
    { value: "identificacion_cliente", viewValue: "ID Cliente" },
    { value: "nombre_cliente_completo", viewValue: "Nombre cliente" },
    { value: "descripcion_estado", viewValue: "Estado pedido" },
    { value: "estado_pago", viewValue: "Estado pago" },
    { value: "valor_pago", viewValue: "Valor pago" },
    { value: "fecha_recoleccion_estimada", viewValue: "Fecha recolección" },
    { value: "fecha_entrega_estimada", viewValue: "Entrega estimada" },
    { value: "direccion_entrega", viewValue: "Dirección entrega" },
    { value: "tipo_entrega", viewValue: "Tipo entrega" }
  ];

  valoresDeTabla: IpedidosJoin[] = [];
  rowClasses: { [key: number]: string } = {};
  destroy$ = new Subject<void>();
  loadingTable: boolean = false;

  // NUEVA PROPIEDAD: Para bloquear el contenido hasta cambio de clave exitoso
  contenidoBloqueado = true; // Inicialmente true; se setea a false después del éxito

  constructor(
    private requestService: RequestService,
    private usermessage: UserMessageService,
    private router: Router,
    private datatransfer: DataService,
    // NUEVAS INYECCIONES
    private dialog: MatDialog, // Para abrir el modal
    private localencript: LocalStorageEncryptationService // Para leer localStorage
  ) { }

  ngOnInit(): void {
    // PRIMERO: Verificar si es primer inicio y manejar modal
    this.verificarYMostrarModalCambioClave();

    // SI NO ESTÁ BLOQUEADO, cargar la tabla (se llamará después del éxito)
    if (!this.contenidoBloqueado) {
      this.getPedidosNoFinalizados();
    }
  }

  // NUEVO MÉTODO: Verifica primer_inicio y abre modal si es necesario
  private verificarYMostrarModalCambioClave(): void {
    const primerInicioStr = this.localencript.getLocalStorage('primer_inicio');
    const token = this.localencript.getLocalStorage(Constantes.tokenKey);
    const idUsuario = this.localencript.getLocalStorage(Constantes.idusuarioKey);

    const esPrimerInicio = primerInicioStr === 'true';

    if (esPrimerInicio && token && idUsuario) {
      // Bloquear contenido inicialmente
      this.contenidoBloqueado = true;

      // Abrir modal bloqueante
      const dialogRef = this.dialog.open(CambiarPasswordObligatorioComponent, {
        width: '400px',
        disableClose: true, // No se puede cerrar sin cambiar (bloquea el sistema)
        data: { 
          token, 
          idUsuario,
          claveTemporal: undefined 
        },
        panelClass: 'modal-bloqueante' // Estilo para z-index alto y backdrop
      });

      // Manejar cierre del modal
      dialogRef.afterClosed()
        .pipe(takeUntil(this.destroy$))
        .subscribe(result => {
          if (result?.success) {
            // ÉXITO: Desbloquear contenido, actualizar token si nuevo, y cargar tabla
            if (result.nuevoToken) {
              this.localencript.setLocalStorage(Constantes.tokenKey, result.nuevoToken);
            }
            this.contenidoBloqueado = false;
            this.usermessage.getToastMessage('success', 'Clave cambiada exitosamente. Bienvenido al home.').fire();
            console.log('Clave cambiada - home desbloqueado');
            
            // Cargar la tabla ahora que está desbloqueado
            this.getPedidosNoFinalizados();
          } else {
            // FALLO o CANCEL: Redirigir a login (forzar cambio)
            this.usermessage.getToastMessage('warning', 'Debes cambiar tu clave para continuar.').fire();
            this.router.navigateByUrl('/bds/login'); // Ajusta a tu ruta de login
          }
        });
    } else {
      // No es primer inicio: Desbloquear inmediatamente y cargar tabla
      this.contenidoBloqueado = false;
      this.getPedidosNoFinalizados();
    }
  }


  getPedidosNoFinalizados() {
    this.loadingTable = true;
    this.requestService.get(Constantes.apiGetPedidosNoFinalizados)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (value) => {
          this.loadingTable = false;
          this.valoresDeTabla = value.data.filter((pedido: IestadosPlataforma) => 
            pedido.descripcion_estado !== 'Entregado' && 
            pedido.descripcion_estado !== 'Entregado con atraso'
          );
        },
        error: () => {
          this.loadingTable = false;
          this.usermessage.getToastMessage('error', 'Error al cargar los pedidos.').fire();
        }
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}