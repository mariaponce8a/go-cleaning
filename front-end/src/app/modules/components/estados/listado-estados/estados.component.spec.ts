import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoEstadosComponent } from './estados.component';

describe('EstadosComponent', () => {
  let component: ListadoEstadosComponent;
  let fixture: ComponentFixture<ListadoEstadosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoEstadosComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoEstadosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
