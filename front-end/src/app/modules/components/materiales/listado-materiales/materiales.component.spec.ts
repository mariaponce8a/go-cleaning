import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoMaterialesComponent } from './materiales.component';

describe('MaterialesComponent', () => {
  let component: ListadoMaterialesComponent;
  let fixture: ComponentFixture<ListadoMaterialesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoMaterialesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoMaterialesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
