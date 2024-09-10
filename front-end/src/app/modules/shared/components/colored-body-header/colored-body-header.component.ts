import { Component, EventEmitter, Input, Output } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { eventListeners } from '@popperjs/core';

@Component({
  selector: 'app-colored-body-header',
  standalone: true,
  imports: [
    MatIconModule
  ],
  templateUrl: './colored-body-header.component.html',
  styleUrl: './colored-body-header.component.css'
})
export class ColoredBodyHeaderComponent {
  @Input() nombrePagina: string = 'opcion';
  @Input() subtitulo: string = 'subtitulo opcion';
  @Input() icon: string = 'folder';

  @Output() returnPage: EventEmitter<string> = new EventEmitter<string>();
  goBack(action: string) {
    this.returnPage.emit(action);
  }
}
