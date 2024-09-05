import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormDescuentosComponent } from './form-descuentos.component';

describe('FormUsuariosComponent', () => {
  let component: FormDescuentosComponent;
  let fixture: ComponentFixture<FormDescuentosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [FormDescuentosComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormDescuentosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
