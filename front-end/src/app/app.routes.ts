import { Routes } from '@angular/router';
import { LoginComponent } from './modules/seguridad/login/login.component';
import { MenuComponent } from './modules/menu/menu.component';
import path from 'path';

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
        children: [
            {
                path: 'pedidos',
                loadComponent: () => import('./modules/components/pedidos/listar-pedidos/listar-pedidos.component').then(c => c.ListarPedidosComponent),     
            } ,
            {
                path: 'usuarios',
                loadComponent: () => import('./modules/components/usuarios-plataforma/listado-usuarios/listado-usuarios.component').then(c => c.ListadoUsuariosComponent),     
            } ,
            {
                path: 'clientes',
                loadComponent: () => import('./modules/components/clientes/listado-clientes/clientes.component').then(c=> c.ListadoClientesComponent)
            } ,
            {
                path: 'descuentos',
                loadComponent: () => import('./modules/components/descuentos/listado-descuentos/descuentos.component').then(c=> c.ListadoDescuentosComponent)
            } ,
            {
                path: 'servicios',
                loadComponent: () => import('./modules/components/servicios/listado-servicios/servicios.component').then(c=> c.ListadoServiciosComponent)
            } ,
            {
                path: 'materiales',
                loadComponent: () => import('./modules/components/materiales/listado-materiales/materiales.component').then(c=> c.ListadoMaterialesComponent)
            } ,
            {
                path: 'estados',
                loadComponent: () => import('./modules/components/estados/listado-estados/estados.component').then(c=> c.ListadoEstadosComponent)
            } ,
            {
                path: 'recomendaciones',
                loadComponent: () => import('./modules/components/recomendaciones/listado-recomendaciones/recomendaciones.component').then(c=> c.ListadoRecomendacionesComponent)
            } ,
            {
                path: 'asignaciones',
                loadComponent: () => import('./modules/components/asignaciones/listado-asignaciones/asignaciones.component').then(c=> c.ListadoAsignacionEmpleadoComponent)
            } ,
        ]
    }

];
