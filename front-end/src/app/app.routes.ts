import { Routes } from '@angular/router'; 
import { MenuComponent } from './modules/menu/menu.component'; 
import { AuthGuard } from './guards/auth.guard';  
export const routes: Routes = [
    {
        path: '', redirectTo: 'login', pathMatch: 'full'
    },
    {
        path: 'login',
        loadComponent: () => import('./modules/seguridad/login/login.component').then(c => c.LoginComponent)
    },
    {
        path: 'bds',
        component: MenuComponent,
        canActivate: [AuthGuard],
        children: [
            {
                path: 'bds',
               canActivate: [AuthGuard], 
                loadComponent: () => import('./modules/components/home/tablas-home/tablas-home.component').then(c => c.TablasHomeComponent),
            },
            {
                path: 'pedidos',
               canActivate: [AuthGuard], 
                loadComponent: () => import('./modules/components/pedidos/listar-pedidos/listar-pedidos.component').then(c => c.ListarPedidosComponent),
            },
            {
                path: 'formulario-pedido',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/pedidos/formulario-pedido/formulario-pedidos.component').then(c => c.FormularioPedidosComponent),
            },
            {
                path: 'usuarios',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/usuarios-plataforma/listado-usuarios/listado-usuarios.component').then(c => c.ListadoUsuariosComponent),
            },
            {
                path: 'clientes',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/clientes/listado-clientes/clientes.component').then(c => c.ListadoClientesComponent)
            },
            {
                path: 'descuentos',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/descuentos/listado-descuentos/descuentos.component').then(c => c.ListadoDescuentosComponent)
            },
            {
                path: 'servicios',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/servicios/listado-servicios/servicios.component').then(c => c.ListadoServiciosComponent)
            },
            {
                path: 'materiales',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/materiales/listado-materiales/materiales.component').then(c => c.ListadoMaterialesComponent)
            },
            {
                path: 'estados',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/estados/listado-estados/estados.component').then(c => c.ListadoEstadosComponent)
            },
            {
                path: 'recomendaciones',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/recomendaciones/listado-recomendaciones/recomendaciones.component').then(c => c.ListadoRecomendacionesComponent)
            },
            {
                path: 'asignaciones',
                canActivate: [AuthGuard],
                loadComponent: () => import('./modules/components/asignaciones/listado-asignaciones/asignaciones.component').then(c => c.ListadoAsignacionEmpleadoComponent)
            },
        ]
    }

];
