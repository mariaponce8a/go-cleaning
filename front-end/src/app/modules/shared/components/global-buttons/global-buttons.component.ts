import { Component, Input, Output } from '@angular/core';
import { EventEmitter } from '@angular/core';

@Component({
  selector: 'app-global-buttons',
  standalone: true,
  imports: [],
  templateUrl: './global-buttons.component.html',
  styleUrl: './global-buttons.component.css'
})
export class GlobalButtonsComponent {
  @Input() textBtn1: string = 'INGRESAR';
  @Input() textBtn2: string = 'CANCELAR';
  @Input() showConfirm: boolean = false;
  @Input() showCancel: boolean = false;
  @Output() btnAction = new EventEmitter<string>()

  sendAction(action: string) {
    this.btnAction.emit(action);
  }

}
