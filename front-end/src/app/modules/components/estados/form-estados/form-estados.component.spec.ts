import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormEstadosComponent } from './form-estados.component';

describe('FormEstadosComponent', () => {
  let component: FormEstadosComponent;
  let fixture: ComponentFixture<FormEstadosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormEstadosComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormEstadosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
