import { Component, Inject, Input } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-image-dialog',
  template: `
    <div class="image-dialog">
      <img [src]="imageUrl" [style.width]="width" [style.height]="height">
    </div>
  `,
  styles: [`
    .image-dialog {
      max-width: 95vw; 
      max-height: 95vh; 
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .image-dialog img {
      max-width: 100%; 
      max-height: 100%; 
      object-fit: contain;
    }
  `]
})
export class ImageDialogComponent {
  imageUrl: string = '';
  width: string = '100%'; 
  height: string = '100%'; 

  constructor(@Inject(MAT_DIALOG_DATA) public data: any) {
    this.imageUrl = data.imageUrl;
    this.width = data.width;
    this.height = data.height;
  }
}