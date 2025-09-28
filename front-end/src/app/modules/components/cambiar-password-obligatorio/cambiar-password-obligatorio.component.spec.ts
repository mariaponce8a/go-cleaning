import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CambiarPasswordObligatorioComponent } from './cambiar-password-obligatorio.component';

describe('CambiarPasswordObligatorioComponent', () => {
  let component: CambiarPasswordObligatorioComponent;
  let fixture: ComponentFixture<CambiarPasswordObligatorioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CambiarPasswordObligatorioComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CambiarPasswordObligatorioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
