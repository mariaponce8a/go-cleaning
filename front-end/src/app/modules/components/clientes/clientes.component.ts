import { Component } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../shared/components/colored-body-header/colored-body-header.component';

@Component({
  selector: 'app-clientes',
  standalone: true,
  imports: [
    ColoredBodyHeaderComponent
  ],
  templateUrl: './clientes.component.html',
  styleUrl: './clientes.component.css'
})
export class ClientesComponent {

}
