import { Component } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../shared/components/colored-body-header/colored-body-header.component';

@Component({
  selector: 'app-descuentos',
  standalone: true,
  imports: [
    ColoredBodyHeaderComponent
  ],
  templateUrl: './descuentos.component.html',
  styleUrl: './descuentos.component.css'
})
export class DescuentosComponent {

}
