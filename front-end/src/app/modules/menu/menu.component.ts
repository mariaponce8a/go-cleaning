import { AfterViewInit, Component, Inject, OnDestroy, OnInit, PLATFORM_ID } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { CommonModule } from '@angular/common';
import { MatSidenavModule } from '@angular/material/sidenav';
import { IMenu } from '../shared/interface/datamodels.interface';
import { Router, RouterModule } from '@angular/router';
import {
  BreakpointObserver,
  Breakpoints,
  BreakpointState,
} from '@angular/cdk/layout';
import { LocalStorageEncryptationService } from '../shared/services/local-storage-encryptation.service';
import { Constantes } from '../config/constantes';
import { HttpClient } from '@angular/common/http';
@Component({
  selector: 'app-menu',
  standalone: true,
  imports: [
    MatToolbarModule,
    MatIconModule,
    MatListModule,
    CommonModule,
    MatSidenavModule,
    RouterModule,
  ],
  templateUrl: './menu.component.html',
  styleUrl: './menu.component.css'
})
export class MenuComponent implements OnInit, OnDestroy, AfterViewInit {
  isMobile: boolean = false;
  nombrePersona?: string | null;
  perfilPersona?: string | null;
  perfilPersonaDesc: string = ''
  menuItems: IMenu[] = [
    {
      descripcion: 'Pedidos',
      icon: 'shopping_cart',
      pagina: 'bds/pedidos',
      perfil: ''
    },
    {
      descripcion: 'Clientes',
      icon: 'account_circle',
      pagina: 'bds/clientes',
      perfil: ''
    },
    {
      descripcion: 'Usuarios',
      icon: 'people',
      pagina: 'bds/usuarios',
      perfil: 'A'
    },
    {
      descripcion: 'Asignaciones',
      icon: 'work',
      pagina: 'bds/asignaciones',
      perfil: ''
    },
    {
      descripcion: 'Recomendaciones',
      icon: 'grade',
      pagina: 'bds/recomendaciones',
      perfil: ''
    },
    {
      descripcion: 'Tipos descuentos',
      icon: 'attach_money',
      pagina: 'bds/descuentos',
      perfil: ''
    },
    {
      descripcion: 'Servicios',
      icon: 'local_laundry_service',
      pagina: 'bds/servicios',
      perfil: ''
    },
    {
      descripcion: 'Materiales',
      icon: 'bubble_chart',
      pagina: 'bds/materiales',
      perfil: ''
    },
    {
      descripcion: 'Recomendacion Lavado',
      icon: 'dry_cleaning',
      pagina: 'bds/recomendaciones',
      perfil: ''
    },
    {
      descripcion: 'Estados',
      icon: 'donut_large',
      pagina: 'bds/estados',
      perfil: ''
    },
  ];

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private http: HttpClient,
    private breakPointObserve: BreakpointObserver,
    private router: Router,
    public localEncriptStorage: LocalStorageEncryptationService
  ) {
  }

  ngOnInit(): void {
    this.breakPointObserve
      .observe([Breakpoints.Web, Breakpoints.TabletLandscape])
      .subscribe((result) => {
        this.isMobile = !result.matches;
      });
  }

  ngAfterViewInit(): void {
    this.nombrePersona = this.localEncriptStorage.getLocalStorage(Constantes.usuarioKey);
    this.perfilPersona = this.localEncriptStorage.getLocalStorage(Constantes.perfilKey);
    console.log(this.nombrePersona, '/', this.perfilPersona);

    if (this.perfilPersona == 'E') {
      this.perfilPersonaDesc = 'Empleado';
    } else {
      this.perfilPersonaDesc = 'Administrador';
    }
  }

  goToPage(pagina: string) {
    console.log(pagina);
    this.router.navigateByUrl(pagina);
  }

  ngOnDestroy(): void {

  }

}
