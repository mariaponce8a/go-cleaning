import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioPedidosComponent } from './formulario-pedidos.component';

describe('FormularioPedidosComponent', () => {
  let component: FormularioPedidosComponent;
  let fixture: ComponentFixture<FormularioPedidosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormularioPedidosComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormularioPedidosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
