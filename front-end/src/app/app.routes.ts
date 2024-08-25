import { Routes } from '@angular/router';
import { LoginComponent } from './modules/seguridad/login/login.component';
import { MenuComponent } from './modules/menu/menu.component';

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
            
        ]
    }

];
