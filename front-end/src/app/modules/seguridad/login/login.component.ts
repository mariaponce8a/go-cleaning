import { Component, OnDestroy } from '@angular/core';
import { FormControl, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { GlobalButtonsComponent } from '../../shared/components/global-buttons/global-buttons.component';
import { RequestService } from '../../shared/services/request.service';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { UserMessageService } from '../../shared/services/user-message.service';
import { Constantes } from '../../config/constantes';
import { Subject, takeUntil } from 'rxjs';
import { LocalStorageEncryptationService } from '../../shared/services/local-storage-encryptation.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    FormsModule,
    MatInputModule,
    MatIconModule,
    MatButtonModule,
    MatCheckboxModule,
    GlobalButtonsComponent,
    HttpClientModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnDestroy {

  public hidePassword: boolean = false;

  destroy$ = new Subject<void>();

  constructor(
    private http: HttpClient,
    private requesService: RequestService,
    private usermessage: UserMessageService,
    private localencript: LocalStorageEncryptationService,
    private router: Router
  ) { }

  changePasswordVisibility() {
    this.hidePassword = !this.hidePassword;
  }

  formlogin = new FormGroup({
    usuario: new FormControl('', [Validators.required]),
    clave: new FormControl('', [Validators.required])
  })

  public handleAction(event: string) {
    console.log(event)
    if (event == 'confirm') {
      if (this.formlogin.invalid) {
        this.usermessage.getToastMessage('info', Constantes.formInvalidMessage).fire()
        this.formlogin.markAllAsTouched();
        return;
      }
      let body = this.formlogin.getRawValue();
      this.requesService.post(body, Constantes.apiLogin)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: (value) => {
            this.localencript.setLocalStorage(Constantes.tokenKey, value.data.token);
            this.localencript.setLocalStorage(Constantes.usuarioKey, value.data.usuario);
            this.localencript.setLocalStorage(Constantes.perfilKey, value.data.perfil);
            this.localencript.setLocalStorage(Constantes.idusuarioKey, String(value.data.id_usuario));
            console.log(value.data.id_usuario)
            this.router.navigateByUrl('/bds/home');

          },
          error: (error) => {
            this.usermessage.getToastMessage('error', error);
          }
        })
    }
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

}
