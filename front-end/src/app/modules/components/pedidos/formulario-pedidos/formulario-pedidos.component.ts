import { Component } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { Router } from '@angular/router';

@Component({
  selector: 'app-formulario-pedidos',
  standalone: true,
  imports: [
    MaterialModule,
    ColoredBodyHeaderComponent
  ],
  templateUrl: './formulario-pedidos.component.html',
  styleUrl: './formulario-pedidos.component.css'
})
export class FormularioPedidosComponent {

  public propositoPagina: string = 'Formulario';
  constructor(
    private router: Router
  ) { }

  goBack(evento: string) {
    if (evento) {
      this.router.navigateByUrl('bds/pedidos');
    }
  }

}
