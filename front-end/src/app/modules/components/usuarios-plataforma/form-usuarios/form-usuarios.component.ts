import { Component, OnDestroy, OnInit } from '@angular/core';
import { ColoredBodyHeaderComponent } from '../../../shared/components/colored-body-header/colored-body-header.component';
import { Router } from 'express';
import { RequestService } from '../../../shared/services/request.service';
import { Subject } from 'rxjs';

@Component({
  selector: 'app-form-usuarios',
  standalone: true,
  imports: [
    ColoredBodyHeaderComponent
  ],
  templateUrl: './form-usuarios.component.html',
  styleUrl: './form-usuarios.component.css'
})
export class FormUsuariosComponent implements OnInit, OnDestroy {
  destroy$ = new Subject<void>();
  constructor(
    private router: Router,
    private requestservice: RequestService
  ) { }

  ngOnInit(): void {

  }

  ngOnDestroy(): void {

  }

}
