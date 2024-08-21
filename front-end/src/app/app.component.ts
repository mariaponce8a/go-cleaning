import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'front-end';
  spalshPage: boolean = true;
  
  ngOnInit(): void {
    this.spalshPage = true;
    setTimeout(() => {
    this.spalshPage = false;      
    }, 1000);
  }
}
