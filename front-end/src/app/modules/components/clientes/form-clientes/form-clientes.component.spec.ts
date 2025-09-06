import { ComponentFixture, TestBed } from '@angular/core/testing';
import { FormClientesComponent } from './form-clientes.component';

describe('FormClientesComponent', () => {
  let component: FormClientesComponent;
  let fixture: ComponentFixture<FormClientesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormClientesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormClientesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
