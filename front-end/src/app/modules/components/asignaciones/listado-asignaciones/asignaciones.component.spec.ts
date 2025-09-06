import { ComponentFixture, TestBed } from '@angular/core/testing';

import {ListadoAsignacionEmpleadoComponent } from './asignaciones.component';

describe('AsignacionesComponent', () => {
  let component: ListadoAsignacionEmpleadoComponent;
  let fixture: ComponentFixture<ListadoAsignacionEmpleadoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoAsignacionEmpleadoComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoAsignacionEmpleadoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
