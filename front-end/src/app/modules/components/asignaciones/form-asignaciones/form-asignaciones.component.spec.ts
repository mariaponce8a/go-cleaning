import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormAsignacionesComponent } from './form-asignaciones.component';

describe('FormAsignacionesComponent', () => {
  let component: FormAsignacionesComponent;
  let fixture: ComponentFixture<FormAsignacionesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormAsignacionesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormAsignacionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
