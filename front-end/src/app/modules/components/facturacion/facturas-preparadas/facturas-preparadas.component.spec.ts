import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FacturasPreparadasComponent } from './facturas-preparadas.component';

describe('FacturasPreparadasComponent', () => {
  let component: FacturasPreparadasComponent;
  let fixture: ComponentFixture<FacturasPreparadasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FacturasPreparadasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FacturasPreparadasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
