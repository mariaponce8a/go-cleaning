import { Component, EventEmitter, Input, Output } from '@angular/core';
import { MaterialModule } from '../../../desginModules/material.module';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-modal-header',
  standalone: true,
  imports: [
    MaterialModule, CommonModule,
        IonicModule
  ],
  templateUrl: './modal-header.component.html',
  styleUrl: './modal-header.component.css'
})
export class ModalHeaderComponent {

  @Input() tituloHeader: string = 'Formulario';
  @Output() accionCerrar: EventEmitter<boolean> = new EventEmitter(false);
  @Input() mostrarBtnCerrar: boolean = true;

  cerrarModal(eventoCerrar: boolean) {
    this.accionCerrar.emit(true);
  }

}
