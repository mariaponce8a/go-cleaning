import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoClientesComponent } from './clientes.component';

describe('ClientesComponent', () => {
  let component: ListadoClientesComponent;
  let fixture: ComponentFixture<ListadoClientesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoClientesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoClientesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
