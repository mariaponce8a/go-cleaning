import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormRecomendacionesComponent } from './form-recomendaciones.component';

describe('FormRecomendacionesComponent', () => {
  let component: FormRecomendacionesComponent;
  let fixture: ComponentFixture<FormRecomendacionesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormRecomendacionesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormRecomendacionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
