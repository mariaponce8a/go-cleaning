import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ColoredBodyHeaderComponent } from './colored-body-header.component';

describe('ColoredBodyHeaderComponent', () => {
  let component: ColoredBodyHeaderComponent;
  let fixture: ComponentFixture<ColoredBodyHeaderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ColoredBodyHeaderComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ColoredBodyHeaderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
