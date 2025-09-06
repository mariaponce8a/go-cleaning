import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListadoDescuentosComponent } from './descuentos.component';

describe('DescuentosComponent', () => {
  let component: ListadoDescuentosComponent;
  let fixture: ComponentFixture<ListadoDescuentosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListadoDescuentosComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ListadoDescuentosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
