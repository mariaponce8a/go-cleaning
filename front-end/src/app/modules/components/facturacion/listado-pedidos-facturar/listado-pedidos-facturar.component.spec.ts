import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoPedidosFacturarComponent } from './listado-pedidos-facturar.component';

describe('ListadoPedidosFacturarComponent', () => {
  let component: ListadoPedidosFacturarComponent;
  let fixture: ComponentFixture<ListadoPedidosFacturarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoPedidosFacturarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoPedidosFacturarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
