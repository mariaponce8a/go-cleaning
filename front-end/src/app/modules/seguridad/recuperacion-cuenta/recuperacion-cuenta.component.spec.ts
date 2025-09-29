import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecuperacionCuentaComponent } from './recuperacion-cuenta.component';

describe('RecuperacionCuentaComponent', () => {
  let component: RecuperacionCuentaComponent;
  let fixture: ComponentFixture<RecuperacionCuentaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RecuperacionCuentaComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RecuperacionCuentaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
