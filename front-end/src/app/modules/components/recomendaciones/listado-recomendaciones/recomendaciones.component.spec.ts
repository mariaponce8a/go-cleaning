import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoRecomendacionesComponent } from './recomendaciones.component';

describe('RecomendacionesComponent', () => {
  let component: ListadoRecomendacionesComponent;
  let fixture: ComponentFixture<ListadoRecomendacionesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoRecomendacionesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoRecomendacionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
