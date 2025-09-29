import { AfterViewInit, Component, HostListener, Inject, OnDestroy, OnInit, PLATFORM_ID } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { CommonModule } from '@angular/common';
import { MatSidenavModule } from '@angular/material/sidenav';
import { IMenu } from '../shared/interface/datamodels.interface';
import { Router, RouterModule } from '@angular/router';
import { UserMessageService } from '../shared/services/user-message.service';
import { MatDialog } from '@angular/material/dialog';
import {
  BreakpointObserver,
  Breakpoints,
  BreakpointState,
} from '@angular/cdk/layout';
import { LocalStorageEncryptationService } from '../shared/services/local-storage-encryptation.service';
import { Constantes } from '../config/constantes';
import { HttpClient } from '@angular/common/http';
import { IonicModule } from '@ionic/angular';
import { NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

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
    IonicModule,
    NgbDropdownModule
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
      descripcion: 'Home',
      icon: 'home',
      pagina: 'bds/home',
      perfil: 'A, E'
    },
    {
      descripcion: 'Pedidos',
      icon: 'shopping_cart',
      pagina: 'bds/pedidos',
      perfil: 'A, E'
    },
    {
      descripcion: 'Facturas',
      icon: 'receipt',
      pagina: 'bds/facturas',
      perfil: 'A, E'
    },
    {
      descripcion: 'Clientes',
      icon: 'account_circle',
      pagina: 'bds/clientes',
      perfil: 'A, E'
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
      perfil: 'A, E'
    },
    
    {
      descripcion: 'Tipos descuentos',
      icon: 'attach_money',
      pagina: 'bds/descuentos',
      perfil: 'A'
    },
    {
      descripcion: 'Servicios',
      icon: 'local_laundry_service',
      pagina: 'bds/servicios',
      perfil: 'A'
    },
    {
      descripcion: 'Materiales',
      icon: 'bubble_chart',
      pagina: 'bds/materiales',
      perfil: 'A'
    },
    {
      descripcion: 'Recomendaciónes',
      icon: 'dry_cleaning',
      pagina: 'bds/recomendaciones',
      perfil: 'A'
    },
    {
      descripcion: 'Estados',
      icon: 'donut_large',
      pagina: 'bds/estados',
      perfil: 'A'
    },
  ];
  dropdownAbierto = false;

  toggleDropdown() {
    this.dropdownAbierto = !this.dropdownAbierto;
  }

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private http: HttpClient,
    private breakPointObserve: BreakpointObserver,
    private router: Router,
    public localEncriptStorage: LocalStorageEncryptationService,
    private localStorageEncryptation: LocalStorageEncryptationService,
    private usermessage: UserMessageService,
    private dialog: MatDialog
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
      this.menuItems = this.menuItems.filter(item => item.perfil !== 'A');
    } else {
      this.perfilPersonaDesc = 'Administrador';
    }
  }

  verPerfil() {
    // Navegar a la página de perfil
    console.log('Ver perfil');
    this.router.navigate(['bds/perfil']);
  }

  editarPerfil() {
    // Navegar a edición de perfil
    console.log('Editar perfil');
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent) {
    const target = event.target as HTMLElement;
    if (!target.closest('.user-dropdown-trigger') && !target.closest('.dropdown-menu-custom')) {
      this.dropdownAbierto = false;
    }
  }

  
  logout(): void {
    this.usermessage.questionMessage(Constantes.logOutQuestion).then((r) => {
      if (r.isConfirmed) {
        localStorage.removeItem('token');
        this.router.navigateByUrl("/login");
        this.removeAll();
      } else {
      }
    });
  }

  setLocalStorageAutomaticLogout(reason: string) {
    this.localStorageEncryptation.setLocalStorage(
      Constantes.automaticLogOutKey,
      reason
    );
  }
  removeAll(): void {
    const keysToRemove = [
      'auth',
      'idusuarioValue',
      'loglevel',
      'perfilvalue',
      'usuarioValue'
    ];
  
    keysToRemove.forEach(key => localStorage.removeItem(key));
  }

  goToPage(pagina: string) {
    console.log(pagina);
    this.router.navigateByUrl(pagina);
  }

  ngOnDestroy(): void {

  }

}
