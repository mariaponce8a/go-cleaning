import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TablasHomeComponent } from './tablas-home.component';

describe('TablasHomeComponent', () => {
  let component: TablasHomeComponent;
  let fixture: ComponentFixture<TablasHomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TablasHomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TablasHomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
